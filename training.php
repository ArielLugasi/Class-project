<?php
// Include your database connection file (db_connection.php)
$conn = require __DIR__ . "/db_connection.php";

// Check if the user is logged in (you should have a proper user authentication mechanism)
session_start();

if (!isset($_SESSION['firstName'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit();
}

// Fetch user information from the database
$firstName = $_SESSION['firstName'];
$sql = "SELECT * FROM users WHERE FirstName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $firstName);
$stmt->execute();
$result = $stmt->get_result();
$userInfo = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/trainingStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Training Videos - DigitalGym</title>
</head>

<body>
    <header>
        <h1>Welcome,
            <?php echo $firstName; ?>!
        </h1>
    </header>
    <main>
        <section>
            <h2>Training videos by category:</h2>
            <ul class="category-grid">
                <li><a href="Hamstrings.php">Hamstrings</a></li>
                <li><a href="Calves.php">Calves</a></li>
                <li><a href="Chest.php">Chest</a></li>
                <li><a href="Back.php">Back</a></li>
                <li><a href="Shoulders.php">Shoulders</a></li>
                <li><a href="Triceps.php">Triceps</a></li>
                <li><a href="Biceps.php">Biceps</a></li>
                <li><a href="Forearms.php">Forearms</a></li>
                <li><a href="Trapezius.php">Trapezius</a></li>
                <li><a href="Abs.php">Abs</a></li>
            </ul>
            <button class="button-89" style="display: block; margin: 0 auto"
                onClick="window.location.href = 'dashboard.php'"> Back </button>
        </section>
    </main>
    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>