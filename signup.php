<?php
    include("db_config.php");

    if(isset($_GET['type'])) {
        $userType = $_GET['type'];
    } else {
        header("Location: index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Signup - Feedback System</title>
</head>
<body>
    <header>
        <h1>Signup</h1>
    </header>
    <main class="signup-form">
        <form action="process_signup.php" method="post">
            <?php
                if ($userType === 'teacher') {
                    echo '<label for="subject-id">Subject ID:</label>';
                    echo '<input type="text" id="subject-id" name="subject-id" required>';
                } elseif ($userType === 'student') {
                    echo '<label for="usn">USN:</label>';
                    echo '<input type="text" id="usn" name="usn" required>';
                }
            ?>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Signup">
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </main>
</body>
</html>
