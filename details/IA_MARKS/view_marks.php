<?php
// Start session
session_start();
$_SESSION['usn']='1sj21cs007';

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

// Check if user is logged in
if (isset($_SESSION['usn'])) {
    $usn = $_SESSION['usn'];
    $marks = getIAMarksByUsn($conn, $usn);

    echo "Your marks are: <br>";
    echo '<table border="1"><tr><th>Subject code</th><th>IA1</th><th>IA2</th><th>IA3</th></tr>';
    foreach ($marks as $row) {
        echo '<tr>';
        echo '<td>' . $row['c_id'] . '</td>';
        echo '<td>' . $row['ia1'] . '</td>';
        echo '<td>' . $row['ia2'] . '</td>';
        echo '<td>' . $row['ia3'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo "You are not logged in.";
}

$conn->close();
?>
