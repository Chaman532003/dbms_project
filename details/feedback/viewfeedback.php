<?php
// Start the session
session_start();

// Assuming you have established a MySQL connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if email is set in session
if (isset($_SESSION['email'])) {
    // Retrieve s_id from the teacher table based on the email stored in the session
    $email = $_SESSION['email'];
    
    // Retrieve s_id from the teacher table based on the email stored in the session
    $query1 = "SELECT s_id FROM teacher WHERE email = '$email'";
    $result1 = $conn->query($query1);
    if ($result1->num_rows > 0) {
        $row1 = $result1->fetch_assoc();
        $s_id = $row1['s_id'];
    } else {
        // Handle the case when s_id is not found for the given email
        echo "s_id not found for the email: $email";
        exit; // Stop further execution
    }
} else {
    // Handle the case when email is not set in session
    echo "Email not found in session.";
    exit; // Stop further execution
}

// Use s_id to retrieve associated c_id from the course table
$query2 = "SELECT c_id FROM course WHERE s_id = '$s_id'";
$result2 = $conn->query($query2);
$row2 = $result2->fetch_assoc();
$c_id = $row2['c_id'];

// Use the obtained c_id to fetch all corresponding average values from the feedback table
$query3 = "SELECT average FROM feedback WHERE c_id = '$c_id'";
$result3 = $conn->query($query3);

// Count the total number of feedback entries
$totalFeedback = $result3->num_rows;

// Initialize an array to count the occurrences of each rating
$ratingCounts = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);

while ($row3 = $result3->fetch_assoc()) {
    // Increment the count for the corresponding rating
    $rating = $row3['average'];
    $ratingCounts[$rating]++;
}

// Close connection
$conn->close();

// Convert the counts into a format suitable for Chart.js
$ratingsData = array_values($ratingCounts);

// Encode the data as JSON for JavaScript consumption
$ratingsDataJSON = json_encode($ratingsData);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Pie Chart</title>
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        canvas {
            margin: 0 auto; /* Center the canvas horizontally */
            display: block;
        }

        .total-feedback {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Feedback Pie Chart</h1>
    <!-- Canvas to render the pie chart -->
    <canvas id="feedbackChart" width="400" height="400"></canvas>

    <!-- Display the total number of feedback entries -->
    <div class="total-feedback">Total Number Of Feedbacks Received: <?php echo $totalFeedback; ?></div>

    <script>
    // Parse the ratings data from PHP
    var ratingsData = <?php echo $ratingsDataJSON; ?>;

    // Get the canvas element
    var ctx = document.getElementById('feedbackChart').getContext('2d');

    // Create the pie chart
    var feedbackChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['1', '2', '3', '4', '5'],
            datasets: [{
                label: 'Feedback Ratings',
                data: ratingsData,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: false, // Adjust as needed
            aspectRatio: 1, // Ensure the chart is square
            title: {
                display: true,
                text: 'Feedback Ratings Distribution',
                fontSize: 24 // Adjust font size as needed
            }
        }
    });
    </script>
</body>
</html>