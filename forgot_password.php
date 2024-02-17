<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href='Style/forgot_passwordStyle.css'>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <title>Password Reset - DigitalGym</title>
</head>

<body>
    <main>
        <h1>Forgot Password</h1>

        <form action="send-password-reset.php" method="post">

            <label for="email">email</label>
            <input type="email" id="email" name="email" required>
            <br>
            <br>
            <button class="button-89" style="display: block; margin: 0 auto">Send</button>

        </form>

        <button class="button-89" style="display: block; margin: 0 auto" onClick="window.location.href = 'login.php'">
            Back to Login </button>
        </section>
    </main>
    <br>
    <br>
    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>