<?php
session_start();
//$_SESSION['email'] = 'kiran@gmail.com';

// Function to connect to MySQL database
function connectToDatabase($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Function to display student numbers with checkboxes
function displayStudentMarksForm($conn) {
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

    echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
    // Check if any options are found
    if ($result->num_rows > 0) {
        echo '<select name="c_id">';
        // Output options for each row in the result set
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row["c_id"] . '">' . $row["c_id"] . '</option>';
        }
        echo '</select>';
        echo '<input type="submit" name="select_course" value="Select course">';
        echo '</form>'; // Close the form tag
    } else {
        echo 'No options available';
    }
}

// Function to handle form submission
function handleFormSubmission($conn) {
    if (isset($_POST['select_course'])) {
        $_SESSION['c_id'] = $_POST['c_id'];
        $c_id = $_SESSION['c_id'];
        $email = $_SESSION['email'];
        $sql = "SELECT usn, ia1, ia2, ia3 FROM enrolled, teacher t, teaches WHERE t.email='$email' AND t.s_id=teaches.s_id AND teaches.c_id=enrolled.c_id AND enrolled.c_id='$c_id'";        
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
            echo '<table border=1>';
            echo '<tr><th>USN</th><th>IA1</th><th>IA2</th><th>IA3</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row["usn"] . '</td>';
                echo '<td><input type="text" name="ia1[]" value="' . $row["ia1"] . '"></td>';
                echo '<td><input type="text" name="ia2[]" value="' . $row["ia2"] . '"></td>';
                echo '<td><input type="text" name="ia3[]" value="' . $row["ia3"] . '"></td>';
                echo '<td><input type="hidden" name="usn[]" value="' . $row["usn"] . '"></td>'; // Store USN in a hidden field
                echo '</tr>';
            }
            echo '</table>';
            echo '<input type="submit" name="update_marks" value="Give marks">';
            echo '</form>'; // Close the form tag

            // Store form data in session
            $_SESSION['form_data'] = true;
        } else {
            echo "0 results";
        }
    }
    // Check if form data is set in the session before calling insertIAmarks
    if (isset($_SESSION['form_data']) && isset($_POST['update_marks'])) {
        // Call the function to insert IA marks
        insertIAmarks($conn);
    }
}

function insertIAmarks($conn) {
    // Use $_SESSION to access form data
    if (isset($_SESSION['form_data'])) {
        $c_id = $_SESSION['c_id'];
        foreach ($_POST['ia1'] as $index => $ia1) {
            $ia1 = $_POST['ia1'][$index];
            $ia2 = $_POST['ia2'][$index];
            $ia3 = $_POST['ia3'][$index];
            $usn = $_POST['usn'][$index]; // Get USN from the form data
            $sql = "UPDATE enrolled SET ia1 = ?, ia2 = ?, ia3 = ? WHERE c_id = ? AND usn = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiiss", $ia1, $ia2, $ia3, $c_id, $usn);
            if ($stmt->execute() !== TRUE) {
                echo "Error updating IA values: " . $conn->error;
            }
        }
    }
}




// Main code execution starts here
$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "project";
$conn = connectToDatabase($servername, $username, $password, $dbname);
displayStudentMarksForm($conn);
handleFormSubmission($conn);
?>
