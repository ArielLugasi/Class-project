<?php
// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['firstName'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit();
}

// Include your database connection file (db_connection.php)
$conn = require __DIR__ . "/db_connection.php";

// Retrieve user information from the session
$firstName = $_SESSION['firstName'];
$userId = $_SESSION['user_id'];

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userInfo = $result->fetch_assoc();
$stmt->close();

// Check if the user role is 'trainee'
$userRole = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<script src="scripts.js"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/profileStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <title>User Profile - DigitalGym</title>
</head>

<body>
    <header>
        <h1>User Profile</h1>
    </header>

    <main>
        <section>
            <form>
                <!-- Display user profile picture -->
                <img src="<?php echo $userInfo['UserProfilePic']; ?>" alt="Profile Picture" width="100" height="100">
                <h2>
                    <?php echo $userInfo['FirstName'] . ' ' . $userInfo['LastName']; ?>
                </h2>
                <p><strong>Email:</strong>
                    <?php echo $userInfo['Email']; ?>
                </p>
                <p><strong>Date of Birth:</strong>
                    <?php echo $userInfo['DateOfBirth']; ?>
                </p>
                <p><strong>Height:</strong>
                    <?php echo $userInfo['Height']; ?> cm
                </p>
                <p><strong>Weight:</strong>
                    <?php echo $userInfo['Weight']; ?> kg
                </p>

                <a href="edit_profile.php">Edit Profile</a>
            </form>
            <br>
            <br>
            <button class="button-89" style="display: block; margin: 0 auto"
                onClick="window.location.href = 'dashboard.php'"> Back </button>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>