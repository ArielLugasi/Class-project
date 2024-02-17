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
        $stmt->bind_param("iis", $userID, $receiverID, $message); // Update sender_id to use userID
        if ($stmt->execute()) {
            // Message sent successfully
            // You can redirect or display a success message here
        } else {
            // Handle the case where the message couldn't be sent
        }
        $stmt->close();
    }

    // Handle work shift submission
    if (isset($_POST['shift_name'], $_POST['trainer'], $_POST['shift_date'], $_POST['shift_time'])) {
        $shiftName = $_POST['shift_name'];
        $trainerID = $_POST['trainer'];
        $shiftDate = $_POST['shift_date'];
        $shiftTime = $_POST['shift_time'];

        // Combine date and time to create a DateTime object
        $shiftDateTimeStr = "$shiftDate $shiftTime";

        // Insert the work shift into the database
        $sql = "INSERT INTO shifts (trainer_id, shift_name, shift_time) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $trainerID, $shiftName, $shiftDateTimeStr);
        if ($stmt->execute()) {
            // Work shift added successfully
            // You can redirect or display a success message here
        } else {
            // Handle the case where the work shift couldn't be added
        }
        $stmt->close();
    }

    // Handle shift deletion
    if (isset($_POST['deleteShift'])) {
        $shiftIdToDelete = $_POST['deleteShift'];

        // Perform the deletion logic here (e.g., delete from the database)
        $deleteShiftSql = "DELETE FROM shifts WHERE id = ?";
        $deleteShiftStmt = $conn->prepare($deleteShiftSql);
        $deleteShiftStmt->bind_param("i", $shiftIdToDelete);
        if ($deleteShiftStmt->execute()) {
            // Shift deleted successfully
            // You can redirect or display a success message here
        } else {
            // Handle the case where the shift couldn't be deleted
        }
        $deleteShiftStmt->close();
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

// Fetch trainers for the dropdown
$sql = "SELECT ID, FirstName FROM users WHERE Role = 'trainer'";
$result = $conn->query($sql);
$trainers = $result->fetch_all(MYSQLI_ASSOC);

// Fetch all shifts for all trainers
$sql = "SELECT users.FirstName AS trainer_name, shifts.shift_name, shifts.shift_time, shifts.id
        FROM shifts
        INNER JOIN users ON shifts.trainer_id = users.ID
        WHERE users.Role = 'trainer'
        ORDER BY users.FirstName, shifts.shift_time ASC";
$result = $conn->query($sql);
$shifts = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/adminStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
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
            <form action="admin.php" method="post">
                <label for="receiver">Receiver:</label>
                <select id="receiver" name="receiver" required>
                    <option value="" disabled selected>Select Receiver</option>
                    <?php
                    // Fetch users from the database to populate the dropdown
                    $sql = "SELECT ID, FirstName, Role FROM users WHERE ID != ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $userID);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $all_users = $result->fetch_all(MYSQLI_ASSOC);
                    $stmt->close();

                    foreach ($all_users as $user) { ?>
                        <option value="<?php echo $user['ID']; ?>">
                            <?php echo $user['FirstName'] . ' (' . $user['Role'] . ')'; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="message">Message:</label>
                <input id="message" name="message" required></input>

                <button class="button-89" type="submit">Send Message</button>
            </form>
        </section>

        <section>
            <h2>Manage Work Shifts</h2>
            <form action="admin.php" method="post">
                <label for="shift_name">Shift Name:</label>
                <input type="text" id="shift_name" name="shift_name" required>

                <label for="trainer">Trainers List:</label>
                <select id="trainer" name="trainer" required>
                    <option value="" disabled selected>Select Trainer</option>
                    <?php foreach ($trainers as $trainer) { ?>
                        <option value="<?php echo $trainer['ID']; ?>">
                            <?php echo $trainer['FirstName']; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="shift_date">Shift Date:</label>
                <input type="date" id="shift_date" name="shift_date" required min="<?php echo date('Y-m-d'); ?>">
                <!-- Set the min attribute to the current date -->

                <label for="shift_time">Shift Time:</label>
                <input type="time" id="shift_time" name="shift_time" required>

                <button class="button-89" type="submit">Add Work Shift</button>
            </form>
        </section>

        <section>
            <h2>Inbox</h2>
            <form>
                <div style="height:200px; overflow:auto;">
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
        </section>

        <section>
            <h2>Trainers Shifts</h2>
            <div id="calendar"></div>
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

    <script>
        $(document).ready(function () {
            // Initialize FullCalendar
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: [
                    <?php
                    foreach ($shifts as $shift) {
                        $shiftName = $shift['shift_name'];
                        $shiftTime = $shift['shift_time'];
                        $trainerName = $shift['trainer_name'];
                        $eventTitle = "{$shiftName} - {$trainerName}";

                        echo "{
                            id: '{$shift['id']}',
                            title: '{$eventTitle}',
                            start: '{$shiftTime}',
                            allDay: true
                        },";
                    }
                    ?>
                ],
                eventClick: function (calEvent, jsEvent, view) {
                    alert('Shift: ' + calEvent.title + '\nDate: ' + moment(calEvent.start).format('DD/MM/YYYY h:mma'));
                    var confirmDelete = confirm("Do you want to delete this shift?");
                    if (confirmDelete) {
                        // Submit the form to handle shift deletion
                        $('<form action="admin.php" method="post">\
                            <input type="hidden" name="deleteShift" value="' + calEvent.id + '">\
                            </form>').appendTo('body').submit();
                    }
                }
            });
        });
    </script>
</body>

</html>