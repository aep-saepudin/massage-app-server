<?php  


    header('Content-Type: application/json');
    $id_pesanan = $_GET['id_pesanan'];
    $client_id = $_GET['client_id']; // u1
    $partner_id = $_GET['partner_id']; // p1
    $pricing_ids = $_GET['pricing_ids']; // 

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

          $client_id   = '';
          $partner_id   = '';
          $total       = '';
          $product_id  = '';
          $pricing_ids = '';


          include '../db/connectDB.php';
          $register = "insert into transaction (
            client_id, 
            partner_id, 
            product_id,	
            date_created,	
            pricing_ids, 
            total_price 
          ) values ('$client_id','$partner_id','$product_id',NOW(),'$pricing_ids','$total')";

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