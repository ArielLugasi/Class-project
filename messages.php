<?php
// Include your database connection file (db_connection.php)
$conn = require __DIR__ . "/db_connection.php";

// Check if the user is logged in
session_start();

if (!isset($_SESSION['firstName'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit();
}

$firstName = $_SESSION['firstName'];
$userID = $_SESSION['user_id']; // Add this line to get the user's ID

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['receiver'], $_POST['message'])) {
        $receiverID = $_POST['receiver'];
        $message = $_POST['message'];

        // You should validate and sanitize user inputs here

        // Insert the message into the database
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $userID, $receiverID, $message);
        if ($stmt->execute()) {
            // Message sent successfully
            // You can redirect or display a success message here
        } else {
            // Handle the case where the message couldn't be sent
        }
        $stmt->close();
    }
}


// Fetch messages sent to the current user (for display in the user's inbox)
$sql = "SELECT messages.id, users.FirstName AS sender, messages.message, messages.timestamp
        FROM messages
        INNER JOIN users ON messages.sender_id = users.ID
        WHERE messages.receiver_id = ?
        ORDER BY messages.timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$userMessages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/messagesStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <title>Messages - DigitalGym</title>
</head>

<body>
    <header>
        <h1>Welcome,
            <?php echo $_SESSION['firstName']; ?>!
        </h1>
    </header>

    <main>
        <section>
            <h2>Send Messages</h2>
            <form action="messages.php" method="post">
                <label for="receiver">Receiver:</label>
                <select id="receiver" name="receiver" required>
                    <option value="" disabled selected>Select Receiver</option>
                    <?php
                    // Fetch all users from the database to populate the dropdown
                    $sql = "SELECT ID, FirstName, Role FROM users";
                    $result = $conn->query($sql);
                    $allUsers = $result->fetch_all(MYSQLI_ASSOC);

                    foreach ($allUsers as $user) {
                        // Exclude the current user from the dropdown
                        if ($user['ID'] != $userID) {
                            // Allow the trainee to send messages to trainers or managers
                            if ($_SESSION['role'] === 'trainee' && $user['Role'] != 'trainee') {
                                ?>
                                <option value="<?php echo $user['ID']; ?>">
                                    <?php echo $user['FirstName'] . ' (' . $user['Role'] . ')'; ?>
                                </option>
                                <?php
                            }
                            // Allow other users to send messages to all except themselves
                            else if ($_SESSION['role'] != 'trainee') {
                                ?>
                                    <option value="<?php echo $user['ID']; ?>">
                                    <?php echo $user['FirstName'] . ' (' . $user['Role'] . ')'; ?>
                                    </option>
                                <?php
                            }
                        }
                    }

                    ?>
                </select>

                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea>

                <button class="button-89" type="submit">Send Message</button>
            </form>
        </section>

        <section>
            <h2>Inbox</h2>
            <form>
                <div class="inbox-container">
                    <table border="1" style="overflow-y:scroll">
                        <tr>
                            <th>Sender</th>
                            <th>Message</th>
                            <th>Timestamp</th>
                        </tr>
                        <?php foreach ($userMessages as $message) { ?>
                            <tr>
                                <td>
                                    <?php echo $message['sender']; ?>
                                </td>
                                <td>
                                    <?php echo $message['message']; ?>
                                </td>
                                <td>
                                    <?php echo $message['timestamp']; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </form>
            <br>
            <br>
            <button class="button-89" style="display:block; margin:0 auto;"
                onClick="window.location.href = 'dashboard.php'"> Back </button>
        </section>
    </main>
    <footer>
        <p>&copy;
            <?php echo date('Y'); ?> DigitalGym. All rights reserved.
        </p>
    </footer>
</body>

</html>