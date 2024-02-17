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

// Function to add a video URL to the database
function addVideoUrl($title, $url, $category, $conn)
{
    // Prepare the SQL statement
    $sql = "INSERT INTO video_urls (title, url, category) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("sss", $title, $url, $category);

    // Execute the statement
    $stmt->execute();

    // Close the statement
    $stmt->close();
}

// Function to delete a video from the database
function deleteVideo($videoId, $conn)
{
    // Prepare the SQL statement to delete the video
    $sql = "DELETE FROM video_urls WHERE id = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("i", $videoId);

    // Execute the statement
    $stmt->execute();

    // Close the statement
    $stmt->close();
}

$currentPage = "calves"; // Updated the category to "Shoulders"

if (isset($userInfo['Role']) && ($userInfo['Role'] == 'manager' || $userInfo['Role'] == 'trainer')) {
    // Manager or Trainer-specific preferences
    $isManagerOrTrainer = true;
} else {
    $isManagerOrTrainer = false;
}
// Handle video submission
if (isset($_POST['title']) && isset($_POST['url'])) {
    $title = $_POST['title'];
    $url = $_POST['url'];

    // Add the video to the database with the current page name as the category
    addVideoUrl($title, $url, $currentPage, $conn);
}

// Handle video deletion (only for managers and trainers)
if ($isManagerOrTrainer && isset($_POST['deleteVideo'])) {
    $videoIdToDelete = $_POST['deleteVideo'];

    // Delete the video from the database
    deleteVideo($videoIdToDelete, $conn);
}

// Retrieve videos based on the current page's category
$sql = "SELECT * FROM video_urls WHERE category = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $currentPage);
$stmt->execute();
$result = $stmt->get_result();
$videoUrls = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>Calves Training Videos - DigitalGym</title>
</head>

<body>
    <header>
        <h1>Welcome,
            <?php echo $firstName; ?>!
        </h1>
    </header>

    <main>
        <section>
            <h2>Calves Training Videos</h2>
            <?php foreach ($videoUrls as $video) { ?>
                <div>
                    <h2>
                        <?php echo $video['title']; ?>
                    </h2>
                    <a class="button-89" href="<?php echo $video['url']; ?>" target="_blank">Watch Video</a>
                    <?php if ($isManagerOrTrainer) { ?>
                        <!-- Delete Video Form for Managers and Trainers -->
                        <form action="Calves.php" method="post">
                            <input type="hidden" name="deleteVideo" value="<?php echo $video['id']; ?>">
                            <br>
                            <br>
                            <button class="btn"><i class="fa fa-trash"></i> Delete</button>
                        </form>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if ($isManagerOrTrainer) { ?>
                <!-- Add Video Form for Managers and Trainers -->
                <div>
                    <h3>Add Calves Video</h3>
                    <form action="Calves.php" method="post">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required>

                        <label for "url">URL:</label>
                        <input type="url" id="url" name="url" required>

                        <button class="button-89" type="submit">Add Video</button>
                    </form>
                </div>
            <?php } ?>
            <button class="button-89" style="display: block; margin: 0 auto"
                onClick="window.location.href = 'training.php'"> Back </button>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>