<?php

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));

$token_hash = hash("sha256", $token);

$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$mysqli = require __DIR__ . "/db_connection.php";

$sql = "UPDATE users
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("sss", $token_hash, $expiry, $email);

$stmt->execute();

if ($mysqli->affected_rows) {

    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("NoReply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END

    Click <a href="http://localhost/PHP%20Works/ProjectGYM-Ariel&Yehuda%20-%20New/reset-password.php?token=$token">here</a> 
    to reset your password.

    END;

    try {

        $mail->send();

    } catch (Exception $e) {

        $confirmationMessage = "Message could not be sent. Mailer error: {$mail->ErrorInfo}";

    }

}

$confirmationMessage = "Message sent. Please check your inbox and follow the link to reset your password.";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send-password-reset</title>
    <link rel="stylesheet" href='Style/process-reset-password.css'>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
</head>

<body>
    <main>
        <h1>Password Reset Message</h1>
        <form action="send-password-reset.php" method="post">
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