<?php
session_start(); // Start or resume the session

// Include your database connection file (db_connection.php)
$conn = require __DIR__ . "/db_connection.php";

// Initialize login status
$_SESSION['login_status'] = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Query the database to retrieve user info
    $sql = "SELECT ID, FirstName, password_hash, Role FROM users WHERE FirstName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $firstName);
    $stmt->execute();
    $stmt->bind_result($userId, $dbUsername, $dbPasswordHash, $userRole);
    $stmt->fetch();
    $stmt->close();

    // Check if all fields are correct
    if (!empty($dbUsername) && password_verify($password, $dbPasswordHash) && $role === $userRole) {
        // Successful login
        $_SESSION['user_id'] = $userId;
        $_SESSION['firstName'] = $dbUsername;
        $_SESSION['role'] = $userRole;
        $_SESSION['login_status'] = 'success'; // Set login status to success
        header('Location: dashboard.php'); // Redirect to user dashboard
        exit();
    } else {
        // Failed login
        $_SESSION['login_status'] = 'error'; // Set login status to error
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href='Style/loginStyle.css'>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <title>Login - DigitalGym</title>
</head>

<body>
    <header>
        <h1>Login to DigitalGym</h1>
    </header>

    <main>
        <section>
            <h2>Login</h2>
            <?php if ($_SESSION['login_status'] === 'error') { ?>
                <p class="error">
                    Invalid username, password, or role. Please try again.
                </p>
            <?php } ?>
            <form action="login.php" method="post">
                <label for="firstName">Username:</label>
                <input type="text" id="firstName" name="firstName" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="role">Role:</label>
                <select id="role" name="role">
                    <option value="trainee">Trainee</option>
                    <option value="trainer">Trainer</option>
                    <option value="manager">Manager</option>
                </select>
                <br>
                <button class="button-89" style="display: block; margin: 0 auto;" type="submit">Login</button>
                <br>
                <br>
            </form>
            <button class=" button-89" style="display: block; margin: 0 auto" ;
                onClick=" window.location.href='index.php'"> Back </button>
            <br>
            <br>
            <button class=" button-89" style="display: block; margin: 0 auto" ; onClick="
                window.location.href='forgot_password.php'">Forgot Password?</button>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>