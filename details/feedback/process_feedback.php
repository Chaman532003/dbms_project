<?php

session_start();

if (isset($_SESSION['email'])) {
    // Retrieve the email
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
        $rating = $_POST['rating'];
        $feedback_text = $_POST['feedback_text'];

        $insert_sql = "INSERT INTO feedback (usn, c_id, rating, feedback_text)
               VALUES ('$usn', '$c_id', '$rating', '$feedback_text')
               ON DUPLICATE KEY UPDATE rating = '$rating', feedback_text = '$feedback_text'";
        
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
