<?php
include 'library/distance.php';
include 'library/arrayOperation.php';
include 'config.php';

header('Content-Type: application/json');


// $BASE_URL = 'https://firestore.googleapis.com/';
// $DATABASE_URL = '/v1/projects/massage-blind/databases/%28default%29/';
// $key = 'AIzaSyBglHISyB36SibOQ2MWH_3SEN-MKwc4_1k';

$user_id    = $_GET['user_id'];                                         // u1
$latitude   = $_GET['latitude'];                                        // 1.2
$longitude  = $_GET['longitude'];                                       // 1.3
$payment    = $_GET['payment'];
$skipped    = isset($_GET['skipped']) ? $_GET['skipped'] : null;        // p1,p2
$id_pesanan = isset($_GET['id_pesanan']) ? $_GET['id_pesanan'] : null;  // dsad7s87da


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
      maters: 1000,
      pid   : p2
    ]
  ]
*/

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


$postfield = '{"fields": {
    "user_id"   : {"stringValue": "'.$user_id.'" },
    "partner_id": {"stringValue": "'.$acceptedPID['pid'].'" },
    "distance"  : {"doubleValue": '. $acceptedPID['meters'] .' },
    "payment"   : {"stringValue": "'. $payment .'" },
    "user_location"  : {
      "geoPointValue":
        {
          "latitude" : '.$latitude.',
          "longitude": '.$longitude.'
        }
    }
}}';

$debug['postfield'] = $postfield;


// add to pesanan
curl_setopt_array($curl, array(
  CURLOPT_URL            => $BASE_URL . $DATABASE_URL. "documents/pesanan?key=$key",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING       => "",
  CURLOPT_MAXREDIRS      => 10,
  CURLOPT_TIMEOUT        => 30,
  CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST  => "POST",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
  ),
  CURLOPT_POSTFIELDS     => $postfield,
));

$debug['addPesanan'] = array(
  "result" => json_decode(curl_exec($curl)),
  "error" => curl_error($curl)
);


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

curl_close($curl);
echo json_encode($debug);