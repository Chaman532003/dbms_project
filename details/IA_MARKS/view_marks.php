<?php
    // Start session
    session_start();

    // Function to connect to MySQL database
    function connectToDatabase($servername, $username, $password, $dbname) {
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    // Function to fetch IA marks for a particular email ID
    function fetchIAMarksByEmail($conn, $email) {
        // Prepare SQL statement
        $sql = "SELECT IA_marks1, IA_marks2, IA_marks3 FROM student WHERE email = ?";
        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        $stmt->bind_param("s", $email);
        
        // Execute statement
        $stmt->execute();
        
        // Bind result variables
        $stmt->bind_result($ia_marks1, $ia_marks2, $ia_marks3);
        
        // Fetch result
        $stmt->fetch();
        
        // Close statement
        $stmt->close();
        
        // Return IA marks as an array
        return array($ia_marks1, $ia_marks2, $ia_marks3);
    }

    // Main code execution starts here
    $servername = "localhost"; 
    $username = "root";
    $password = "";
    $dbname = "project";

    $conn = connectToDatabase($servername, $username, $password, $dbname);

   // $_SESSION['email'] = 'chaman@gmail.com';


    // Check if user is logged in and email is set in session
    if(isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        // Fetch IA marks for the logged-in user
        $ia_marks = fetchIAMarksByEmail($conn, $email);
        
        // Display IA marks to the user
        echo "IA Marks 1: " . $ia_marks[0] . "<br>";
        echo "IA Marks 2: " . $ia_marks[1] . "<br>";
        echo "IA Marks 3: " . $ia_marks[2] . "<br>";
    } else {
        echo "You are not logged in.";
    }

    // Close MySQL connection
    $conn->close();
?>
