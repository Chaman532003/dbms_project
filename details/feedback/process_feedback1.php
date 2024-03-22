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
        $q1 = $_POST['q1'];
        $q2 = $_POST['q2'];
        $q3 = $_POST['q3'];
        $q4 = $_POST['q4'];
        $q5 = $_POST['q5'];
        $q6 = $_POST['q6'];
        $q7 = $_POST['q7'];
        $q8 = $_POST['q8'];
        $q9 = $_POST['q9'];
        $q10 = $_POST['q10'];
        $average = ($q1 + $q2 + $q3 + $q4 + $q5 + $q6 + $q7 + $q8 + $q9 + $q10)/10;

        $insert_sql = "INSERT INTO feedback (usn, c_id, average)
               VALUES ('$usn', '$c_id', '$average') ON DUPLICATE KEY UPDATE average='$average'
            ";
        
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
