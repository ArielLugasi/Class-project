<?php
// Include your database connection file (db_connection.php)
$conn = require __DIR__ . "/db_connection.php";

// Check if the user is logged in as a trainer
session_start();

if (!isset($_SESSION['firstName'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit();
}

$trainerID = $_SESSION['user_id']; // Get the trainer's ID from the session

// Fetch the shifts for the specified trainer
$sql = "SELECT shift_name, shift_time FROM shifts WHERE trainer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trainerID);
$stmt->execute();
$result = $stmt->get_result();
$trainerShifts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/trainer_shiftsStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <title>Trainer Shifts - DigitalGym</title>
</head>

<body>
    <header>
        <h1>Welcome,
            <?php echo $_SESSION['firstName']; ?>!
        </h1>
    </header>

    <main>
        <section>
            <h2>Trainer Shifts</h2>
            <p>Shifts for Trainer:
                <?php echo $_SESSION['firstName']; ?>
            </p>
            <div id="calendar"></div>
            <button class="button-89" style="display:block; margin: 0 auto;"
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
                defaultView: 'month',
                defaultDate: new Date(),
                editable: false, // Disable editing
                events: [ // Use the events array: [
                    <?php
                    // Loop through trainerShifts and format them as FullCalendar events
                    foreach ($trainerShifts as $shift) {
                        $shiftName = $shift['shift_name'];
                        $shiftTime = $shift['shift_time'];

                        echo "{
                        title: '{$shiftName}',
                        start: '{$shiftTime}', // Use the shift_time directly
                        allDay: false
                    },";
                    }
                    ?>
                ],
                eventClick: function (calEvent, jsEvent, view) {
                    alert('Shift: ' + calEvent.title + '\nDate: ' + moment(calEvent.start).format('DD/MM/YYYY h:mma'));
                }
            });
        });
    </script>

</body>

</html>