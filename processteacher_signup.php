<?php
$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$usn = $_POST['usn'];
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];


$usn = mysqli_real_escape_string($conn, $usn);
$name = mysqli_real_escape_string($conn, $name);
$email = mysqli_real_escape_string($conn, $email);


$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


$sql = "INSERT INTO teacher (s_id, name, email, password) VALUES ('$usn', '$name', '$email', '$password');";

if ($conn->query($sql) === TRUE) {
    header("Location: teachers_login.html");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


$conn->close();
?>
