<?php
include '../config.php';

header('Content-Type: application/json');

$id_pesanan = $_GET['id_pesanan'];  // v5ekpGHkAn2m7wDnVMMx

// /*
// Delete pesanan ini
if (isset($id_pesanan)) {
  
    $curl_delete = curl_init();
    curl_setopt_array($curl_delete, array(
      CURLOPT_URL            => $BASE_URL . $DATABASE_URL. "documents/pesanan/$id_pesanan?key=$key",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST  => "DELETE",
    ));
  
    $error = curl_error($curl_delete);
    $result = array(
      "code"      => isset($error) ? 404 : 200,
      "result"    => json_decode(curl_exec($curl_delete)),
      "error"     => curl_error($curl_delete),
      "variables" => $BASE_URL . $DATABASE_URL. "documents/pesanan/$id_pesanan?key=$key"
    );
    curl_close($curl_delete);

  } else {

    $result = array(
        "code"   => 404,
        "error"  => "Id pesanan required"
      );

  }
  // */

echo json_encode($result);