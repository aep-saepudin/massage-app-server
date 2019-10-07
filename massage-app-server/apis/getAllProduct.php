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

    $query = "SELECT * FROM product";
    
    $result = $conn->query($query);

    $data = array();
    if($result->num_rows > 0){

        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
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