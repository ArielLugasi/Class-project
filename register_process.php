<?php
// Connect to the MySQL database (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password = '';
$database = "users_gym";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user input from the registration form
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
$dateOfBirth = $_POST['dateOfBirth'];
$height = $_POST['height'];
$weight = $_POST['weight'];
$role = $_POST['role'];
$profilePicture = $_POST['profilePicture'];

// Check if the email already exists in the database
$emailExistsQuery = "SELECT ID FROM users WHERE Email = '$email'";
$emailExistsResult = $conn->query($emailExistsQuery);

if ($emailExistsResult->num_rows > 0) {
    $confirmationMessage = "Email already exists. Please try again with a different email address. <br> Click <a href='register.php'>here</a> to register.";
} else {
    // Check if the profile picture URL is empty; if it is, set the default URL
    if (empty($profilePicture)) {
        $profilePicture = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png";
    }

    // Insert user data into the database
    $sql = "INSERT INTO users (FirstName, LastName, Email, password_hash, DateOfBirth, Height, Weight, Role, userProfilePic)
            VALUES ('$firstName', '$lastName', '$email', '$password', '$dateOfBirth', '$height', '$weight', '$role', '$profilePicture')";

    if ($conn->query($sql) === TRUE) {
        $confirmationMessage = "Registration successful! <br> Click <a href='login.php'>here</a> to login.";
    } else {
        $confirmationMessage = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Process</title>
    <!-- Link to your CSS file -->
    <link rel="stylesheet" href='Style/register_processStyle.css'>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <!-- Include additional styles here if needed -->
</head>

<body>
    <main>
        <h1>Registration Process</h1>
        <form action="register_process.php" method="post">
            <label>
                <?php echo $confirmationMessage; ?>
            </label>
        </form>
    </main>
    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>