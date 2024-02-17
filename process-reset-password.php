<?php
$token = $_POST["token"];
$token_hash = hash("sha256", $token);
$mysqli = require __DIR__ . "/db_connection.php";
$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    $confirmationMessage = "Token not found";
} elseif (strtotime($user["reset_token_expires_at"]) <= time()) {
    $confirmationMessage = "Token has expired";
} elseif (strlen($_POST["password"]) < 8) {
    $confirmationMessage = "Password must be at least 8 characters";
} elseif (!preg_match("/[a-z]/i", $_POST["password"])) {
    $confirmationMessage = "Password must contain at least one letter";
} elseif (!preg_match("/[0-9]/", $_POST["password"])) {
    $confirmationMessage = "Password must contain at least one number";
} elseif ($_POST["password"] !== $_POST["password_confirmation"]) {
    $confirmationMessage = "Passwords must match";
} else {
    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $sql = "UPDATE users
            SET password_hash = ?,
                reset_token_hash = NULL,
                reset_token_expires_at = NULL
            WHERE ID = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $password_hash, $user["ID"]);

    if ($stmt->execute()) {
        // Password update was successful
        $confirmationMessage = "Password updated successfully. You can now <a href='login.php'>login</a>.";
    } else {
        // Password update failed, display the error
        $confirmationMessage = "Error updating password: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Confirmation</title>
    <link rel="stylesheet" href='Style/process-reset-password.css'>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
</head>

<body>
    <main>
        <h1>Password Reset Confirmation</h1>
        <form action="process-reset-password.php" method="post">
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