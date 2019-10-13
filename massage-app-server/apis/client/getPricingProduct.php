<?php 
    
    $product_id = $_GET['pid'];
    header('Content-Type: application/json');   

    if(!isset($product_id)){
        echo json_encode(array(
            "code"    => 400,
            "message" => "Pid required",
        ));
        die();
    }
    
    
    include '../config.php';

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        $return = array(
            "code"    => 400,
            "status"  => false,
            "message" => "Error in system"
        );
        echo json_encode($return);
        die();
    } 
    

    $query = "SELECT pricing.*, items.item_name , child_item.name 
    FROM `pricing`LEFT JOIN child_item ON child_item.id = pricing.child_item_id LEFT JOIN items ON items.id = child_item.item_id
    WHERE product_id=" . $product_id ;
     

    $result = $conn->query($query);

    $data = array();
    if($result->num_rows > 0){

        while($row = $result->fetch_assoc()) {
            $data[$row["item_name"]][] = $row;
        }
        
        $return = array(
            "code"   => 200,
            "status" => true,
            "data"   => $data
        );

    } else {

        $return = array(
            "status" => false,
            "message" => "Data tidak ada"
        );

    }

    $conn->close();

    echo json_encode($return);
?>