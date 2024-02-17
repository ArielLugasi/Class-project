<?php
session_start(); // Start or resume the session

// Include your database connection file (db_connection.php)
$conn = require __DIR__ . "/db_connection.php";

// Check if the user is logged in
if (!isset($_SESSION['firstName'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit();
}

// Retrieve user information from the session
$firstName = $_SESSION['firstName'];

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE FirstName = ?"; // Replace with your actual table name
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $firstName);
$stmt->execute();
$result = $stmt->get_result();
$userInfo = $result->fetch_assoc();
$stmt->close();

// Fetch user schedule data from the database
$userSchedule = []; // Initialize an empty array

if ($userInfo) {
    // Assuming you have a 'schedule' table in your database
    $sql = "SELECT * FROM schedule WHERE user_id = ?"; // Replace with your actual table name and user_id column
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userInfo['ID']);
    $stmt->execute();
    $scheduleResult = $stmt->get_result();

    // Fetch and organize the schedule data by date
    while ($row = $scheduleResult->fetch_assoc()) {
        $userSchedule[$row['date']] = [
            'id' => $row['id'],
            // Add the ID here
            'task' => $row['task'],
            'notes' => $row['notes']
        ];
    }

    $stmt->close();
}

// Handle schedule item addition form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $task = $_POST['task'];
    $notes = $_POST['notes'];

    // Insert the schedule item into the database (assuming you have a 'schedule' table)
    $sql = "INSERT INTO schedule (user_id, date, task, notes) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $userInfo['ID'], $date, $task, $notes);
    $stmt->execute();
    $stmt->close();

    // Refresh the page to show the updated schedule
    header('Location: schedule.php');
    exit();
}

// Handle schedule item deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $itemId = $_GET['delete'];

    // Delete the schedule item from the database
    $sql = "DELETE FROM schedule WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $stmt->close();

    // Redirect to the schedule page
    header('Location: schedule.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<script src="scripts.js"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/scheduleStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

    <title>Schedule - DigitalGym</title>
</head>

<body>
    <header>
        <h1>Welcome,
            <?php echo $firstName; ?>!
        </h1>
    </header>

    <main>
        <section>
            <h2>Your Schedule</h2>
            <form>
                <h3>Your Schedule</h3>
                <div id="calendar"></div>
            </form>
            <h3>Add to Schedule</h3>
            <form action="schedule.php" method="post">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>

                <label for="task">Task:</label>
                <input type="text" id="task" name="task" required>

                <label for="notes">Notes:</label>
                <textarea id="notes" name="notes"></textarea>

                <button class="button-89" type="submit">Add to Schedule</button>
            </form>
            <button class="button-89" style="display: block; margin: 0 auto"
                onClick="window.location.href = 'dashboard.php'"> Back </button>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>

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
                // Loop through userSchedule and generate events in FullCalendar format
                foreach ($userSchedule as $date => $schedule) {
                    $formattedDate = date('Y-m-d', strtotime($date));
                    echo "{
                             id: {$schedule['id']},
                             title: '{$schedule['task']}',
                             notes: '{$schedule['notes']}',
                             start: '{$formattedDate}',
                             allDay: true
                          },";
                }
                ?>
            ],
            eventClick: function (calEvent, jsEvent, view) {
                alert('Task: ' + calEvent.title + '\nDate: ' + moment(calEvent.start).format('YYYY-MM-DD') + '\nNotes:' + calEvent.notes);
                var confirmDelete = confirm("Do you want to delete this task?");
                if (confirmDelete) {
                    // Redirect to delete task
                    window.location.href = 'schedule.php?delete=' + calEvent.id;
                }
            }
        });
    });
</script>