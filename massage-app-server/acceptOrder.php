<?php
include 'library/distance.php';
include 'library/arrayOperation.php';

header('Content-Type: application/json');

$BASE_URL = 'https://firestore.googleapis.com/';
$DATABASE_URL = '/v1/projects/massage-blind/databases/%28default%29/';
$key = 'AIzaSyBglHISyB36SibOQ2MWH_3SEN-MKwc4_1k';

$user_id    = $_GET['user_id'];     // u1
$partner_id = $_GET['partner_id'];  // p4
$payment    = $_GET['payment'];     // bank , cod
$id_pesanan = $_GET['id_pesanan'];


if ($payment == 'bank'){
    $status     = 'waiting_transfer';      // waiting_transfer , on_the_way , doing, finish
} else {
    $status     = 'on_the_way';
}

$debug = array();
$curl = curl_init();

$postfield = '{"fields": {
    "user_id"   : {"stringValue": "'.$user_id.'" },
    "partner_id": {"stringValue": "'.$partner_id.'" },
    "status"    : {"stringValue": "'. $status .'" },
    "payment"   : {"stringValue": "'. $payment .'" },
}}';


// add to pesanan
curl_setopt_array($curl, array(
  CURLOPT_URL            => $BASE_URL . $DATABASE_URL. "documents/pesananClient?key=$key",
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

$debug['activePesanan'] = array(
  "result" => json_decode(curl_exec($curl)),
  "error"  => curl_error($curl)
);

// /*
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
// */

curl_close($curl);
echo json_encode($debug['activePesanan']);