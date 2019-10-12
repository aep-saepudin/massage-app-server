<?php 
    include './config.php';

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        $return = array(
            "status" => false,
            "message" => "Error in system"
        );
        die();
    } 

?>