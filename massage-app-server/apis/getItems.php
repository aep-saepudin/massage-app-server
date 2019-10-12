<?php 
    include 'config.php';
    header('Content-Type: application/json');

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        $return = array(
            "status" => false,
            "message" => "Error in system"
        );
        die();
    } 

    $pid = $_GET['pid'];

    $query = "SELECT pricing.product_id , child_item.name , pricing.price, items.item_name
    FROM pricing 
    LEFT JOIN child_item ON child_item.id = pricing.child_item_id LEFT JOIN items ON items.id = child_item.item_id
    WHERE pricing.product_id = $pid";
    
    $result = $conn->query($query);

    $data = array();
    $data2 = array();
    if($result->num_rows > 0){

        while($row = $result->fetch_assoc()) {

            $data[$row['item_name']][] = array(
                "name" => $row['name'],
                "price" =>$row['price']
            );
        }
        
        $return = array(
            "status" => true,
            "data"   => $data,
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