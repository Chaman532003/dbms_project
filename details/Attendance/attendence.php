<?php

$_SESSION['email'] = 'kiran@gmail.com';
    // Function to connect to MySQL database
    function connectToDatabase($servername, $username, $password, $dbname) {
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

// Function to handle form submission
function handleFormSubmission($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['attendance'])) {
            $attendanceList = $_POST['attendance'];
            $sql = "UPDATE enrolled SET attendance = attendance + 1 WHERE enrolled.c_id = '21cs52' andusn IN ('" . implode("','", $attendanceList) . "')";
            if ($conn->query($sql) !== TRUE) {
                echo "Error updating records: " . $conn->error;
            } else {
                echo "Attendance updated successfully.";
            }
            // Redirect to prevent form resubmission on refresh
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "No attendance selected.";
        }
    }
}


// Function to display student numbers with checkboxes
    function displayStudentAttendanceForm($conn) {
        

        // Retrieve the s_id from the session
        if(isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
            // Execute SQL query
            $sql = "SELECT s_id FROM teacher WHERE email = ? ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            // Check if result exists
            if ($result->num_rows > 0) {
                // Fetch result and store in variable
                $row = $result->fetch_assoc();
                $s_id = $row['s_id'];
                // Now $usn contains the value from the SQL query
                // echo "USN: " . $usn;
            } else {
                echo "No records found.";
            }
        } else {
            echo "You are not logged in.";
        }


// Query to fetch options from the teaches table for the given s_id
$sql = "SELECT c_id FROM teaches WHERE s_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $s_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if any options are found
if ($result->num_rows > 0) {
    echo '<select name="c_id">';
    // Output options for each row in the result set
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row["c_id"] . '">' . $row["c_id"] . '</option>';
    }
    echo '</select>';
} else {
    echo 'No options available';
}


        echo '<form method="post" action="attendence.php">';

        $email = $_SESSION['email'];
        $sql = "SELECT usn FROM enrolled,teacher t, teaches where t.email='$email' and t.s_id=teaches.s_id and teaches.c_id=enrolled.c_id and enrolled.c_id='21cs52';";        
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo '<form method="post" action="attendence.php">'; // Set the form method to "post" and action to the processing script
            echo '<table border=1>';
            echo '<tr><th>USN</th><th>Attendance</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row["usn"] . '</td>';
                echo '<td><input type="checkbox" name="attendance[]" value="' . $row["usn"] . '"></td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '<input type="submit" value="Submit">';
            echo '</form>'; // Close the form tag
        } else {
            echo "0 results";
        }
    }
    

    // Main code execution starts here
    $servername = "localhost"; 
    $username = "root";
    $password = "";
    $dbname = "project";
    $conn = connectToDatabase($servername, $username, $password, $dbname);
    displayStudentAttendanceForm($conn);
    handleFormSubmission($conn);
?>
