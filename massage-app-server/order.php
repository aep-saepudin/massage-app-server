<?php



function insertFirebasePesanan ($data, $partner_id, $distance){
  if($data){
    $user_id    = $data['user_id'];                                         // u1
    $latitude   = $data['latitude'];                                        // 1.2
    $longitude  = $data['longitude'];                                       // 1.3
    $payment    = $data['payment'];
  } 
  
  $products_string = '';
  if ($data){
    $reduce = '';
    $array_expression = $data['products']['options'];
    $i = 0;
    foreach ($array_expression as $key => $value) {
      $last = $i == 0 ? ',' : '';
      $reduce .= '"'. strtolower(str_replace(" ","_",$key)).'" : {"integerValue": '. $value .' }'. $last;
      $i++;
    }
  
    $products_string = '"products"   : { 
      "mapValue": {
        "fields": {
          '.$reduce.'
        }
      } 
    },';
  }
  
  $postfield = '{"fields": {
    "user_id"   : {"stringValue": "'.$user_id.'" },
    "partner_id": {"stringValue": "'.$partner_id.'" },
    "distance"  : {"doubleValue": '.$distance.' },
    "payment"   : {"stringValue": "'. $payment .'" },
    "user_location"  : {
      "geoPointValue":
        {
          "latitude" : '.$latitude.',
          "longitude": '.$longitude.'
        }
    },
    '.$products_string.'
    "name" : {"stringValue": "'. $data['products']['name'] .'" }
  }}';
  
  $curl = curl_init();
  
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://firestore.googleapis.com/v1/projects/massage-blind/databases/%28default%29/documents/pesanan/?key=AIzaSyBglHISyB36SibOQ2MWH_3SEN-MKwc4_1k",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $postfield,
    CURLOPT_HTTPHEADER => array(
      "Content-Type: application/json",
    ),
  ));
  
    $response = curl_exec($curl);
    $err = curl_error($curl);
  
    curl_close($curl);
  
    if ($err) {
      return $err;
    } else {
      return $response;
    }
}

include 'library/distance.php';
include 'library/arrayOperation.php';
include 'config.php';

header('Content-Type: application/json');


// $BASE_URL = 'https://firestore.googleapis.com/';
// $DATABASE_URL = '/v1/projects/massage-blind/databases/%28default%29/';
// $key = 'AIzaSyBglHISyB36SibOQ2MWH_3SEN-MKwc4_1k';


//{"latitude":-6.88034609999999968721340337651781737804412841796875,"longitude":107.590040799999997034319676458835601806640625,"payment":"tunai","user_id":"u1","products":{"options":{"Durasi":"2000","Jenis Kelamin":"1000"},"name":"Full Body Massage"}}}
$data = json_decode( file_get_contents( 'php://input' ), true );



$user_id    = $_GET['user_id'] or '';                                         // u1
$latitude   = $_GET['latitude'] or '';                                        // 1.2
$longitude  = $_GET['longitude'] or '';                                       // 1.3
$payment    = $_GET['payment'] or '';
$skipped    = isset($_GET['skipped']) ? $_GET['skipped'] : null;        // p1,p2
$id_pesanan = isset($_GET['id_pesanan']) ? $_GET['id_pesanan'] : null;  // dsad7s87da

if($data){
  $user_id    = $data['user_id'];                                         // u1
  $latitude   = $data['latitude'];                                        // 1.2
  $longitude  = $data['longitude'];                                       // 1.3
  $payment    = $data['payment'];
} 

$debug = array();

$debug['params'] = array(
  $user_id,
  $latitude,
  $longitude,
  $payment,
  $skipped,
  $id_pesanan,
);
$curl = curl_init();

// get active partner
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_URL, $BASE_URL . $DATABASE_URL . "documents/activePartner?key=$key"); 

$response = curl_exec($curl);
$decoded = json_decode($response);


$debug['getActivePartner'] = array(
  "result" => json_decode($response),
  "error"  => curl_error($curl)
);

if ( !count((array)$decoded)) {
  $debug['error'] = "Tidak ada terapis yang aktif";

  curl_close($curl);
  echo json_encode($debug);    
  die();
  
}


// filterActive
if (isset($skipped)){
  
  $arrSkipped = explode(',', $skipped);
  $decoded->documents = array_filter($decoded->documents, function($val){
    global $arrSkipped;
    $pid = lastArrayString($val->name);  
    return !in_array($pid ,$arrSkipped);
  });

}


/*
  {
      "getActivePartner": {
          "result": {
              "documents": [
                  {
                      "name": "projects/massage-blind/databases/(default)/documents/activePartner/p1",
                      "fields": {
                          "lokasi": {
                              "geoPointValue": {
                                  "latitude": -6.880324,
                                  "longitude": 107.5900434
                              }
                          }
                      },
                      "createTime": "2019-09-03T22:37:36.590224Z",
                      "updateTime": "2019-09-03T22:37:36.590224Z"
                  },
                  {
                      "name": "projects/massage-blind/databases/(default)/documents/activePartner/p2",
                      "fields": {
                          "lokasi": {
                              "geoPointValue": {
                                  "latitude": 0.1,
                                  "longitude": 0.1
                              }
                          }
                      },
                      "createTime": "2019-09-03T22:26:56.520984Z",
                      "updateTime": "2019-09-03T22:38:28.679944Z"
                  }
              ]
          },
          "error": ""
      }
  }
*/


$arrayPoints = array_map(function($data){
  return array(
    'lat'  => $data->fields->lokasi->geoPointValue->latitude, 
    'long' => $data->fields->lokasi->geoPointValue->longitude,
    'pid'   => lastArrayString($data->name)
  );
},$decoded->documents); 


/*
  $arrayPoints = array(
    array('lat' => 40.758224, 'long' => -73.917404, "id"=>1),
    array('lat' => 40.758224, 'long' => -73.917424, "id"=>2),
    array('lat' => 40.758224, 'long' => -73.917434, "id"=>3),
  );
*/


function calculateDistance($value){
  global $latitude, $longitude;
  $distance = getDistanceBetweenPoints($latitude, $longitude, $value['lat'], $value['long']);
  return  array("meters" => $distance['meters'], "pid"=> $value['pid']);
}

$arrayDistance = array_map('calculateDistance', $arrayPoints);

/*
  $arrayDistance = [
    [
      meters: 1000,
      pid   : p2
    ]
  ]
*/

// limit pencarian berdasarkan jarak 5Km
if ($arrayDistance['meters'] > 5000) {
  
  echo json_encode(array(
    "code"    => 400,
    "data"    => $arrayDistance,
    "message" => "Lebih dari 5km"
  ));
  die();
}


$min = array_reduce($arrayDistance, function($min, $details) {
  return min($min, $details['meters']);
}, PHP_INT_MAX);

/* 1000 */

$acceptedPID = array_filter( $arrayDistance, function($val){
  global $min;
  return $val["meters"] == $min;
});

$acceptedPID = end($acceptedPID);

/* 
  [
      maters: 1000,
      pid   : p2
  ]
*/

$debug['add_data_wow'] = insertFirebasePesanan($data, $acceptedPID['pid'], $acceptedPID['meters']  );

// Delete pesanan lama
if (isset($id_pesanan)) {
  
  $curl_delete = curl_init();
  curl_setopt_array($curl_delete, array(
    CURLOPT_URL            => $BASE_URL . $DATABASE_URL. "documents/pesanan/$id_pesanan?key=$key",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => "DELETE",
  ));

  $debug['hapusPesanan'] = array(
    "result" => json_decode(curl_exec($curl_delete)),
    "error"  => curl_error($curl_delete)
  );
  curl_close($curl_delete);
}

$debug['product'] = $data['products'];

curl_close($curl);
echo json_encode($debug);