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

    // Function to update IA marks for each USN in the student table
    function updateIAMarks($conn, $usn, $test1, $test2, $test3) {
        $sql = "UPDATE student SET IA_marks1 = ?, IA_marks2 = ?, IA_marks3 = ? WHERE USN = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $test1, $test2, $test3, $usn);
        $stmt->execute();
        $stmt->close();
    }

    // Main code execution starts here
    $servername = "localhost"; 
    $username = "root";
    $password = "";
    $dbname = "project";

    $conn = connectToDatabase($servername, $username, $password, $dbname);

    // Check if "update marks" button is clicked
    if(isset($_POST['update_marks'])) {
        // Loop through each USN and update IA marks if provided
        $sql = "SELECT USN FROM student";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $usn = $row["USN"];
                $test1 = isset($_POST['test1_' . $usn]) ? $_POST['test1_' . $usn] : null;
                $test2 = isset($_POST['test2_' . $usn]) ? $_POST['test2_' . $usn] : null;
                $test3 = isset($_POST['test3_' . $usn]) ? $_POST['test3_' . $usn] : null;
                updateIAMarks($conn, $usn, $test1, $test2, $test3);
            }
            echo "IA marks updated successfully.";
        } else {
            echo "0 results";
        }
    }

    // Close MySQL connection
    $conn->close();
?>
<form action="" method="post">
    <?php
        // Assuming you have already connected to the database and retrieved student data
        // Display input fields for each test and hidden fields to send data to PHP script
        $conn = connectToDatabase($servername, $username, $password, $dbname);
        $sql = "SELECT USN FROM student";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $usn = $row["USN"];
                echo '<p>USN: ' . $usn . '</p>';
                echo '<label for="test1_' . $usn . '">Test 1:</label>';
                echo '<input type="text" id="test1_' . $usn . '" name="test1_' . $usn . '"><br>';
                echo '<label for="test2_' . $usn . '">Test 2:</label>';
                echo '<input type="text" id="test2_' . $usn . '" name="test2_' . $usn . '"><br>';
                echo '<label for="test3_' . $usn . '">Test 3:</label>';
                echo '<input type="text" id="test3_' . $usn . '" name="test3_' . $usn . '"><br><br>';
                echo '<input type="hidden" name="usn[]" value="' . $usn . '">';
            }
        } else {
            echo "0 results";
        }
    ?>
    <input type="submit" name="update_marks" value="Update Marks">
</form>
