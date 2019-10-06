<?php 

include 'config.php';
header('Content-Type: application/json');

$email    = $_POST['email'];
$password = md5($_POST['password']);
$tipe     = $_POST['tipe']; // partner, user

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    $return = array(
        "status" => false,
        "message" => "Error in system"
    );
    echo json_encode($return);
    die();
} 

$login = "select * from client where email='$email' and password='$password' and tipe='$tipe'";
$result = $conn->query($login);


$data = array();
if($result->num_rows > 0){
    
    while($row = $result->fetch_assoc()) {
        
        if ($tipe == 'user') {
            $data["id"] = 'u' . $row["id"];            
        } else if ($tipe == 'partner') {
            $data["id"] = 'p' . $row["id"];
        }

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