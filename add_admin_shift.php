<?php
// Include your database connection file (db_connection.php)
$conn = require __DIR__ . "/db_connection.php";

// Check if the user is logged in (you should have a proper user authentication mechanism)
session_start();

if (!isset($_SESSION['firstName'])) {
    header('Location: login.php'); // Redirect to the admin login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shiftName = $_POST['shiftName'];
    $shiftTime = $_POST['shiftTime'];

    // Fetch admin information from the session
    $firstName = $_SESSION['firstName'];
    $sql = "SELECT * FROM users WHERE FirstName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $firstName);
    $stmt->execute();
    $result = $stmt->get_result();
    $adminInfo = $result->fetch_assoc();
    $stmt->close();

    // Add the new shift to the database for the admin
    $sql = "INSERT INTO shifts (admin_id, shift_name, shift_time) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $adminInfo['ID'], $shiftName, $shiftTime);

    if ($stmt->execute()) {
        // Shift added successfully
        header('Location: admin.php'); // Redirect back to the admin page
        exit();
    } else {
        // Error occurred while adding the shift
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// If the request is not POST, redirect back to the admin page
header('Location: admin.php');
exit();
?>