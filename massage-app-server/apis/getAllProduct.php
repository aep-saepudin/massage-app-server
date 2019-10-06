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

    $query = "SELECT product.name as pname, items.item_name, child_item.id, child_item.name, pricing.price  
    FROM `child_item`, pricing, items, product , product_items
    WHERE 
        child_item.item_id = items.id AND 
        product.id = pricing.product_id AND
        pricing.child_item_id = child_item.id AND
        product_items.product_id = product.id AND
        product_items.item_id = items.id";
    
    $result = $conn->query($query);


    $data = array();
    if($result->num_rows > 0){
        
        $i = 0;
        $items = array();
        $products = array();

        while($row = $result->fetch_assoc()) {

            // seriously this fucking hard algorithm
            
            $findItems = array_filter(
                $items,
                function ($e) {
                    global $row;
                    return $e["name"] == $row['item_name'];
                }
            );


            $child = array(
                "id"    => $row['id'],
                "name"  => $row['name'],
                "price" => $row['price']
            );

            if (empty($findItems)) {
                array_push($items , array( "name"  => $row['item_name'], "child" => array($child)) );
            } else {
                foreach($items as $index => $value) {
                    if ($value["name"] == $row['item_name']) {
                        array_push($items[$index]["child"] , $child );
                        break;
                    }
                }
            }


            $findProduct = array_filter(
                $products,
                function ($e) {
                    global $row;
                    return $e["name"] == $row['pname'];
                }
            );


            if (empty($findProduct)) {
                array_push($products , 
                    array( 
                            "name"  => $row['pname'], 
                            "image" => '',
                            "items" => array( $items )
                        ) 
                    );
            } else {
                foreach($products as $index => $value) {
                    if ($value["name"] == $row['pname']) {
                        array_push($products[$index]["items"] , $items );
                        break;
                    }
                }
            }

        } // end while

        $data = $products;

        $return = array(
            "status" => true,
            "data"   => $data
        );

    } else {

        $return = array(
            "status" => false,
            "message" => "Password atau Email Salah"
        );

    }

    $conn->close();

    echo json_encode($return);
?>