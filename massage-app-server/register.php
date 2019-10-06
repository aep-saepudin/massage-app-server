<?php 
include 'config.php';
header('Content-Type: application/json');

$email    = $_POST['email'];
$telepon  = $_POST['telepon'];
$password = md5($_POST['password']);


// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);


if ($conn->connect_error) {
    $return = array(
        "status" => false,
        "message" => "Error in system"
    );
    die();
} 


$register = "insert into client (email,telepon,password) values ('$email','$telepon','$password')";

if($conn->query($register) === TRUE){
    
    $return = array(
        "status" => true
    );

} else {

    $return = array(
        "status" => false,
    );

}

$conn->close();

echo json_encode($return);
?>