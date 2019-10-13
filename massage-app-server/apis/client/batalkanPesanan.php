<?php  

    header('Content-Type: application/json');
    $id_pesanan = $_GET['id_pesanan'];

    // delete firestore active pesanan
    if (isset($id_pesanan)) {
        include '../config.php';

        // cek pesanan ada atau tidak
        $curl_delete = curl_init();
        curl_setopt_array($curl_delete, array(
            CURLOPT_URL            => $BASE_URL . $DATABASE_URL. "documents/pesananClient/$id_pesanan?key=$key",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "GET",
          ));

        $response = json_decode(curl_exec($curl_delete));

        if (isset($response->error)) {
            $result['code'] = 400;
            $result['data'] = json_decode(curl_exec($curl_delete));
            $result['message'] = "lihat detail";

            echo json_encode($result);
            die();

        } else {
          
          $detailPesanan = json_decode(curl_exec($curl_delete));
          curl_setopt_array($curl_delete, array(
            CURLOPT_URL            => $BASE_URL . $DATABASE_URL. "documents/pesananClient/$id_pesanan?key=$key",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "DELETE",
          ));
          curl_exec($curl_delete);
          curl_close($curl_delete);


          $result['code']    = 200;
          $result['message'] = "Sukses Delete Data";
        }

      } else  {

        $result['code'] = 400;
        $result['message'] = 'Pesanan ID tidak ada';
        echo json_encode($result);
        die();
    }
    

    // insert to db order
    echo json_encode($result);
?>