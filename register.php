<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - DigitalGym</title>
    <link rel="stylesheet" href='Style/registerStyle.css'>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
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

        // Function to validate the password
        function validatePassword() {
            var passwordInput = document.getElementById("password");
            var password = passwordInput.value;

            // Define regular expressions for password requirements
            var hasLetter = /[a-zA-Z]/.test(password);
            var hasNumber = /\d/.test(password);

            // Validate password length and requirements
            if (password.length < 8 || !hasLetter || !hasNumber) {
                alert("Password must be at least 8 characters and contain at least one letter and one number.");
                passwordInput.value = ""; // Clear the invalid password
                return false;
            }
            return true;
        }

    </script>
</head>

<body>
    <header>
        <h1>Register for DigitalGym</h1>
    </header>
    <main>
        <section>
            <h2>Registration Form</h2>
            <form action="register_process.php" method="post" onsubmit="return validatePassword();">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required><br><br>

                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br><br>

                <label for="dateOfBirth">Date of Birth:</label>
                <input type="date" id="dateOfBirth" name="dateOfBirth" required>

                <label for="height">Height (in cm):</label>
                <input type="number" id="height" name="height" required>

                <label for="weight">Weight (in kg):</label>
                <input type="number" id="weight" name="weight" required>

                <label for="profilePicture">Profile Picture URL:</label>
                <input type="text" id="profilePicture" name="profilePicture"><br><br>

                <label for="role">Role:</label>
                <select id="role" name="role">
                    <option value="trainee">Trainee</option>
                    <option value="trainer">Trainer</option>
                    <!-- <option value="manager">Manager</option> -->
                </select>
                <br>
                <button class="button-89" style=" display: block; margin: 0 auto;" type="submit">Register</button>
            </form>
            <br>
            <button class="button-89" style=" display: block; margin: 0 auto;"
                onClick="window.location.href='index.php'"> Back </button>

        </section>
    </main>
    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>