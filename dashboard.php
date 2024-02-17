<?php
session_start(); // Start or resume the session

// Check if the user is logged in
if (!isset($_SESSION['firstName'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit();
}

// Retrieve user information from the session
$firstName = $_SESSION['firstName'];
$userRole = $_SESSION['role'];

// Define a function to fetch user information from the database
function fetchUserInfo($firstName, $conn)
{
    // Implement a database query to fetch user details based on $username
    $sql = "SELECT * FROM users WHERE FirstName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $firstName);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInfo = $result->fetch_assoc();
    $stmt->close();
    return $userInfo;
}

// Include your database connection file (db_connection.php)

$conn = require __DIR__ . "/db_connection.php";

// Fetch user details from the database
$userInfo = fetchUserInfo($firstName, $conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/dashboardStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <title>Dashboard - DigitalGym</title>
</head>

<body>
    <header>
        <h1>Welcome,
            <?php echo $firstName; ?>!
        </h1>
    </header>

    <main>
        <section>
            <h2>
                <?php echo ucfirst($userRole); ?> Dashboard
            </h2>
            <!-- Display user-specific content here -->
            <p>Hello
                <?php echo ucfirst($userRole); ?>! Here's your dashboard content.
            </p>
            <ul>
                <li><a href="profile.php">Profile</a></li>
                <br>
                <li><a href="nutrition.php">Nutrition</a></li>
                <br>
                <li><a href="schedule.php">Schedule</a></li>
                <br>
                <li><a href="training.php">Training</a></li>
                <br>
                <?php if ($userRole !== 'manager') { ?>
                    <li><a href="messages.php">Messages</a></li>
                    <br>
                <?php }
                if ($userRole === 'manager') { ?>
                    <li><a href="admin.php">Admin</a></li>
                <?php }
                if ($userRole === 'trainer') { ?>
                    <li><a href="trainer_shifts.php">Trainer_shifts</a></li>
                <?php } ?>
            </ul>
            <button class="button-89" style="display: block; margin: 0 auto;" class="button-27"
                onClick="window.location.href = 'login.php'"> Log out </button>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>