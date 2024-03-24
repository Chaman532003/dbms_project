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

    // Function to retrieve attendance for logged-in student based on email
    function getAttendanceByUsn($conn, $usn) {
        $sql = "SELECT attendance FROM enrolled WHERE usn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usn);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $attendanceArray = array(); // Initialize an array to store attendance values
    
        if ($result->num_rows > 0) {
            // Loop through each row in the result set and fetch attendance values
            while ($row = $result->fetch_assoc()) {
                $attendanceArray[] = $row["attendance"];
                
            }
            return $attendanceArray; // Return the array of attendance values
        } else {
            return "0 results";
        }
    }
    

    // Main code execution starts here
    $servername = "localhost"; 
    $username = "root";
    $password = "";
    $dbname = "project";

    $conn = connectToDatabase($servername, $username, $password, $dbname);

 //$_SESSION['email'] = 'amoghrgowda09@gmail.com';
 //$_SESSION['usn'] = '1sj21cs007';


    if(isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        // Execute SQL query
        $sql = "SELECT usn FROM student WHERE email = ? ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        // Check if result exists
        if ($result->num_rows > 0) {
            // Fetch result and store in variable
            $row = $result->fetch_assoc();
            $usn = $row['usn'];
            // Now $usn contains the value from the SQL query
            // echo "USN: " . $usn;
        } else {
            echo "No records found.";
        }
    } else {
        echo "You are not logged in.";
    }

    

    // Check if user is logged in
    if(isset($_SESSION['usn'])) {
        $usn = $_SESSION['usn'];
        $cid = getCidByUsn($conn, $usn);
$attendance = getAttendanceByUsn($conn, $usn);

echo '<div style="width: 100%; text-align: center; font-size: 24px; margin-bottom: 20px;">Your (' . $usn . ') attendance is:</div>';

// Check if both arrays have the same length
if (count($cid) == count($attendance)) {
    echo '<div style="width: 100%; height: 100vh; display: flex; justify-content: center; align-items: center; background-color: #1a1a1a;">';
    echo '<table style="border-collapse: collapse; width: 80%; color: #ffffff; text-align: center;">';
    echo '<tr style="background-color: #333333;"><th style="padding: 20px; border: 1px solid #ffffff; font-size: 20px;">Subject code</th><th style="padding: 20px; border: 1px solid #ffffff; font-size: 20px;">Attendance</th></tr>';
    for ($i = 0; $i < count($cid); $i++) {
        echo '<tr style="background-color: ' . ($i % 2 == 0 ? '#444444' : '#555555') . ';"><td style="padding: 10px; border: 1px solid #ffffff;">' . $cid[$i] . '</td><td style="padding: 10px; border: 1px solid #ffffff;">' . $attendance[$i] . '</td></tr>';
    }
    echo '</table>';
    echo '</div>';
}

 else {
    echo "Error: Subject codes and attendance values don't match.";
}

    } else {
        echo "You are not logged in.";
    }



    function getCidByUsn($conn, $usn) {
        $sql = "SELECT c_id FROM enrolled WHERE usn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usn);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $cidArray = array(); // Initialize an array to store attendance values
    
        if ($result->num_rows > 0) {
            // Loop through each row in the result set and fetch attendance values
            while ($row = $result->fetch_assoc()) {
                $cidArray[] = $row["c_id"];
                
            }
            return $cidArray; // Return the array of attendance values
        } else {
            return "0 results";
        }
    }
    


    /*if(isset($_SESSION['usn'])) {
        $usn = $_SESSION['usn'];
        $cid = getCidByUsn($conn, $usn);
        echo "the subject code is: <br>" . implode("<br> ", $cid); // Echo the attendance value
    } else {
        echo "You are not logged in.";
    }*/



    $conn->close();
?>
