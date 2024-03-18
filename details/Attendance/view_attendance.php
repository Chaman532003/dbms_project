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

   // $_SESSION['email'] = 'amoghrgowda09@gmail.com';


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

echo "Your attendance is: <br>";

// Check if both arrays have the same length
if (count($cid) == count($attendance)) {
    // Iterate over both arrays simultaneously
    echo "<table border=1><th>Subject code</th><th>Attendance</th>";
    for ($i = 0; $i < count($cid); $i++) {
        echo "<tr><td>" . $cid[$i] . "</td><td>" . $attendance[$i] . "</td></tr><br>";
    }echo "</table>";
} else {
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
