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

// Update profile data if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated profile information from the form
    $newFirstName = $_POST['first_name'];
    $newLastName = $_POST['last_name'];
    $newEmail = $_POST['email'];
    $newDateOfBirth = $_POST['dateOfBirth'];
    $newHeight = $_POST['height'];
    $newWeight = $_POST['weight'];
    if ($_POST['profile_pic']) {
        $newProfilePic = $_POST['profile_pic']; // Updated profile picture URL
    } else {
        $newProfilePic = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png";
    }
    // Update the user's profile data in the database
    $updateSql = "UPDATE users SET FirstName = ?, LastName = ?, Email = ?, DateOfBirth = ?, Height = ?, Weight = ?, UserProfilePic = ? WHERE ID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssssddsi", $newFirstName, $newLastName, $newEmail, $newDateOfBirth, $newHeight, $newWeight, $newProfilePic, $userId);

    if ($updateStmt->execute()) {
        // Profile data updated successfully
        header('Location: profile.php'); // Redirect to the profile page
        exit();
    } else {
        // Error occurred while updating profile data
        $errorMessage = "Error updating profile: " . $updateStmt->error;
    }

    $updateStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<script src="scripts.js"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/edit_profileStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <title>Edit Profile - DigitalGym</title>
    <script>
        // Calculate the minimum and maximum allowed birth dates
        var currentDate = new Date();
        var minDate = new Date(currentDate);
        minDate.setFullYear(currentDate.getFullYear() - 120); // Minimum age: 120 years
        var maxDate = new Date(currentDate);
        maxDate.setFullYear(currentDate.getFullYear() - 12); // Maximum age: 12 years

        // Set the min and max attributes for the date input
        window.onload = function () {
            var dateOfBirthInput = document.getElementById("dateOfBirth");
            dateOfBirthInput.min = formatDate(minDate);
            dateOfBirthInput.max = formatDate(maxDate);
        };

        // Function to format the date as "YYYY-MM-DD"
        function formatDate(date) {
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, "0");
            var day = String(date.getDate()).padStart(2, "0");
            return year + "-" + month + "-" + day;
        }
    </script>
</head>

<body>
    <header>
        <h1>Edit Profile</h1>
    </header>

    <main>
        <section>
            <form method="post" action="edit_profile.php">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo $userInfo['FirstName']; ?>"
                    required>

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo $userInfo['LastName']; ?>"
                    required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $userInfo['Email']; ?>" required>

                <label for="dateOfBirth">Date of Birth:</label>
                <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo $userInfo['DateOfBirth']; ?>"
                    required>

                <label for="height">Height (cm):</label>
                <input type="number" id="height" name="height" value="<?php echo $userInfo['Height']; ?>" required>

                <label for="weight">Weight (kg):</label>
                <input type="number" id="weight" name="weight" value="<?php echo $userInfo['Weight']; ?>" required>

                <label for="profile_pic">Profile Picture URL:</label>
                <input type="url" id="profile_pic" name="profile_pic"
                    value="<?php echo $userInfo['UserProfilePic']; ?>">

                <button class="button-89" type="submit">Save</button>
            </form>

            <?php if (isset($errorMessage)) { ?>
                <p class="error">
                    <?php echo $errorMessage; ?>
                </p>
            <?php } ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>