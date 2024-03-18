<?php
$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

$email = mysqli_real_escape_string($conn, $email);

$sql = "SELECT password FROM student WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashedPasswordFromDatabase = $row['password'];

    if ($password == $hashedPasswordFromDatabase){
        echo "Login successful!";
    } else {
        echo "Invalid password or email!";
    }
} else {
    echo "User not found. Please check your email.";
} 

session_start();

$_SESSION['email'] = $email;
$conn->close();
?>
