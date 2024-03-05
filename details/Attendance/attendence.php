<?php
                // Connect to MySQL database
                $servername = "localhost"; 
                $username = "root";
                $password = "";
                $dbname = "project";

                $conn = new mysqli($servername, $username, $password, $dbname);
            
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
            
                // Query to select all student numbers from the database
                $sql = "SELECT USN FROM student";
                $result = $conn->query($sql);
            
                // Display each student number with a checkbox
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<input type="checkbox" name="attendance[]" value="' . $row["USN"] . '">' . $row["USN"] . '<br>';
                    }
                } else {
                    echo "0 results";
                }
            
                // Close MySQL connection
                $conn->close();
                ?>