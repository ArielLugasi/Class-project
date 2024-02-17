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
    $receiverId = $_POST['receiver'];
    $message = $_POST['message'];

    // Fetch admin information from the session
    $firstName = $_SESSION['firstName'];
    $sql = "SELECT * FROM users WHERE FirstName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $firstName);
    $stmt->execute();
    $result = $stmt->get_result();
    $adminInfo = $result->fetch_assoc();
    $stmt->close();

    // Insert the new message into the messages table
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $adminInfo['ID'], $receiverId, $message);

    if ($stmt->execute()) {
        // Message sent successfully
        header('Location: admin.php'); // Redirect back to the admin page
        exit();
    } else {
        // Error occurred while sending the message
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// If the request is not POST, redirect back to the admin page
header('Location: admin.php');
exit();
?>