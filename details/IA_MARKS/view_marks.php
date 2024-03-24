<?php
// Start session
session_start();
//$_SESSION['usn']='1sj21cs007';
$email = $_SESSION['email'];

// Function to connect to MySQL database
function connectToDatabase($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Function to retrieve ia marks for logged-in student based on usn
function getIAMarksByUsn($conn, $usn) {
    $sql = "SELECT c_id, ia1, ia2, ia3 FROM enrolled WHERE usn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usn);
    $stmt->execute();
    $result = $stmt->get_result();

    $marksArray = array(); // Initialize an array to store marks

    if ($result->num_rows > 0) {
        // Loop through each row in the result set and fetch marks
        while ($row = $result->fetch_assoc()) {
            $marksArray[] = $row;
        }
        return $marksArray; // Return the array of marks
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

$sql = "SELECT usn FROM student WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if result exists
if ($result->num_rows > 0) {
    // Fetch and display the USN value
    $row = $result->fetch_assoc();
    $usn = $row["usn"];
    $_SESSION['usn'] = $usn;
} else {
    echo "No records found for email $email";
}

// Check if user is logged in
if (isset($_SESSION['usn'])) {
    $usn = $_SESSION['usn'];
    $marks = getIAMarksByUsn($conn, $usn);

    echo '<div style="width: 100%; text-align: center; font-size: 24px; margin-bottom: 20px;">Your marks are:</div>';
    echo '<table border="1" style="border-collapse: collapse; width: 80%; margin: 0 auto;">';
    echo '<tr style="background-color: #333333; color: #ffffff; text-align: center;"><th style="padding: 10px;">Subject code</th><th style="padding: 10px;">IA1</th><th style="padding: 10px;">IA2</th><th style="padding: 10px;">IA3</th></tr>';
    $i = 0;
    foreach ($marks as $row) {
        $i++;
        echo '<tr style="background-color: ' . ($i % 2 == 0 ? '#444444' : '#555555') . '; color: #ffffff; text-align: center;"><td style="padding: 10px; border: 1px solid #ffffff;">' . $row['c_id'] . '</td><td style="padding: 10px; border: 1px solid #ffffff;">' . $row['ia1'] . '</td><td style="padding: 10px; border: 1px solid #ffffff;">' . $row['ia2'] . '</td><td style="padding: 10px; border: 1px solid #ffffff;">' . $row['ia3'] . '</td></tr>';
    }
    echo '</table>';
}

 else {
    echo "You are not logged in.";
}

$conn->close();
?>
