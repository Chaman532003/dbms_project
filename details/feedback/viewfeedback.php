<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
    <style>
        body {
            background-color: #121212; 
            color: #ffffff; 
            font-family: 'Arial', sans-serif;
            margin: 0; 
        }

        .container {
            width: 95%; 
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ffffff;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #333; 
            color: #ffffff;
        }

        tr:nth-child(even) {
            background-color: #444; 
        }

        tr:nth-child(odd) {
            background-color: #222; 
        }
    </style>
</head>
<body>

<div class="container">
    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usn = $_POST['usn'];

        $servername = "localhost"; 
        $username = "root";
        $password = "";
        $dbname = "project";

        $conn = new mysqli($servername, $username, $password, $dbname);

        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT c.c_id, f.rating, f.feedback_text
                FROM feedback f
                JOIN course c ON f.c_id = c.c_id
                WHERE f.usn = '$usn'";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<h2>Feedback given by $usn</h2>";
            echo "<table>
                  <tr>
                  <th>Subject Code</th>
                  <th>Rating</th>
                  <th>Feedback</th>
                  </tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['c_id'] . "</td>";
                echo "<td>" . $row['rating'] . "</td>";
                echo "<td>" . $row['feedback_text'] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No feedback found for $usn</p>";
        }

        $conn->close();
    }
    ?>
</div>

</body>
</html>
