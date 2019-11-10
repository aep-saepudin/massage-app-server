<?php  


    header('Content-Type: application/json');
    $id_pesanan = $_GET['id_pesanan'];
    $client_id = $_GET['client_id']; // u1
    $partner_id = $_GET['partner_id']; // p1


    $data = json_decode( file_get_contents( 'php://input' ), true );

    if ($data) {
      $id_pesanan = $data['id_pesanan'];
      $client_id = $data['client_id']; // u1
      $partner_id = $data['partner_id']; // p1
      $products = json_encode($data['products']); // p1
    }

    // delete firestore active pesanan
    if (isset($id_pesanan)) {
        include '../config.php';

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


          
          // Insert ke db

          $total       = '2000';
          $products = json_encode($data['products']); // p1


          include '../db/connectDB.php';
          $register = "insert into transaction (
            client_id, 
            partner_id, 
            product_id,	
            date_created,	
            pricing_ids, 
            total_price 
          ) values ('$client_id','$partner_id','$product_id',NOW(),'$products','$total')";

          if($conn->query($register) === TRUE){
    
            $result = array(
                "code"    => 200,
                "message" => 'Sukses insert'
            );
        
          } else {
              $result = array(
                  "code"    => 400,
                  "message" => $conn->error,
              );
          
          }
        
          $conn->close();

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