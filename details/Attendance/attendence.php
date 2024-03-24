<?php
session_start();
//$_SESSION['email'] = 'svn@gmail.com';

// Function to connect to MySQL database
function connectToDatabase($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
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
        } else {
            echo "No records found.";
            return;
        }
    } else {
        echo "You are not logged in.";
        return;
    }

    // Query to fetch options from the teaches table for the given s_id
    $sql = "SELECT c_id FROM teaches WHERE s_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $s_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<form method="post" action="attendence.php">';
    // Check if any options are found
    if ($result->num_rows > 0) {
        echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" style="text-align: center;">'; // Center the form horizontally
        echo '<select name="c_id" style="padding: 10px; border-radius: 5px; background-color: #f0f0f0; border: 1px solid #ccc; margin-bottom: 10px;">'; // Style the select dropdown
        // Output options for each row in the result set
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row["c_id"] . '">' . $row["c_id"] . '</option>';
        }
        echo '</select>';
       
        echo '<input type="submit" value="Select course" style="padding: 10px 20px; border-radius: 5px; background-color: #4CAF50; color: #ffffff; border: none; cursor: pointer;">'; // Style the submit button
        echo '</form>'; // Close the form tag
        if (isset($_POST['c_id'])) {
            $_SESSION['c_id'] = $_POST['c_id'];
        }
    } else {
        echo 'No options available';
    }
    
}

// Function to handle form submission
function handleFormSubmission($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_SESSION['c_id'])) {
            $c_id = $_SESSION['c_id'];
            $email = $_SESSION['email'];
            $sql = "SELECT usn FROM enrolled, teacher t, teaches WHERE t.email='$email' AND t.s_id=teaches.s_id AND teaches.c_id=enrolled.c_id AND enrolled.c_id='$c_id'";        
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo '<form method="post" action="attendence.php">';
                echo '<table style="border-collapse: collapse; width: 100%; border: 1px solid #000;">';
                echo '<tr style="background-color: #333333; color: #ffffff;"><th style="padding: 10px;">USN</th><th style="padding: 10px;">Attendance</th></tr>';
                while ($row = $result->fetch_assoc()) {
                    echo '<tr style="background-color: #444444; color: #ffffff;">';
                    echo '<td style="padding: 10px; border: 1px solid #ffffff;">' . $row["usn"] . '</td>';
                    echo '<td style="padding: 10px; border: 1px solid #ffffff;"><input type="checkbox" name="attendance[]" value="' . $row["usn"] . '"></td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '<div style="text-align: center; margin-top: 10px;"><input type="submit" value="Give attendance" style="padding: 10px 20px; background-color: #4CAF50; color: #ffffff; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s;"></div>';
                echo '</form>'; 
            } else {
                echo "0 results";
            }
        } else {
            echo "Course ID not set.";
        }
    }

     if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['attendance'])) {
            $attendanceList = $_POST['attendance'];
            $c_id = $_SESSION['c_id'];
            $sql = "UPDATE enrolled SET attendance = attendance + 1 WHERE enrolled.c_id = '$c_id' and usn IN ('" . implode("','", $attendanceList) . "')";
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


// Main code execution starts here
$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "project";
$conn = connectToDatabase($servername, $username, $password, $dbname);
displayStudentAttendanceForm($conn);
handleFormSubmission($conn);
?>
