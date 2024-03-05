<?php

session_start();

if (isset($_SESSION['email'])) {
    // Retrieve the email
    $data = json_decode(file_get_contents('php://input'), true);
    $average = $data['average'];
    $email = $_SESSION['email'];

    $servername = "localhost"; 
    $username = "root";
    $password = "";
    $dbname = "project";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT usn FROM student WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $usn = $row['usn'];

        $c_id = $_POST['c_id'];
        /* $rating = $_POST['rating'];
        $feedback_text = $_POST['feedback_text']; */

        $insert_sql = "INSERT INTO feedback (usn, c_id, average)
               VALUES ('$usn', '$c_id', '$average')
               ON DUPLICATE KEY UPDATE average = '$average'";
        
        if ($conn->query($insert_sql) === TRUE) {
            header("Location:../details.html");
    
        } else {
            echo "Error: " . $insert_sql . "<br>" . $conn->error;
        }

        $conn->close();
    } else {
        echo "User not found.";
    }
} else {
    header("Location: login.php");
    exit();
}
?>
