<?php
session_start();

$conn = require __DIR__ . "/db_connection.php";

if (!isset($_SESSION['firstName'])) {
    header('Location: login.php');
    exit();
}

$firstName = $_SESSION['firstName'];
$foodName = isset($_POST['foodName']) ? $_POST['foodName'] : "";
$nutritionData = null;


// Fetch user details from the database
$sql = "SELECT DateOfBirth, Height, Weight FROM users WHERE FirstName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $firstName);
$stmt->execute();
$result = $stmt->get_result();
$userInfo = $result->fetch_assoc();
$stmt->close();

// Calculate BMR based on user's data
if ($userInfo) {
    $dob = new DateTime($userInfo['DateOfBirth']);
    $today = new DateTime();
    $age = $dob->diff($today)->y;
    $weight = $userInfo['Weight']; // in kg
    $height = $userInfo['Height']; // in cm

    // Calculate BMR using the Mifflin-St Jeor Equation
    $bmr = 10 * $weight + 6.25 * $height - 5 * $age;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($foodName)) {
    // Use the API to fetch nutrition data for the searched food
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://nutrition-by-api-ninjas.p.rapidapi.com/v1/nutrition?query=" . urlencode($foodName),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: nutrition-by-api-ninjas.p.rapidapi.com",
            "X-RapidAPI-Key: f8daa1e377msh2090666bcb99438p12a9b5jsn2f667fa06844"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if (!$err) {
        $nutritionData = json_decode($response, true);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/nutritionStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family<Pacifico&display=swap" rel="stylesheet">
    <title>Nutrition Plans - DigitalGym</title>
</head>

<body>
    <header>
        <h1>Welcome,
            <?php echo $firstName; ?>!
        </h1>
    </header>
    <main>
        <section>

            <h3>The recommended daily amount is:</h3>
            <h3> Protein : between
                <?php echo $weight * 1.7; ?> and
                <?php echo $weight * 2; ?> protein grams per a day
            </h3>
            <h3> Calories : between
                <?php echo $weight * 25; ?> and
                <?php echo $weight * 35; ?> calories per a day
            </h3>
            <h2>Nutrition Plans</h2>
            <form method="post">
                <label for="foodName">Enter a Food:</label>
                <input type="text" id="foodName" name="foodName">
                <button class="button-89" type="submit">Search</button>
            </form>
            <?php if ($nutritionData !== null) { ?>
                <h3>Nutrition Information for
                    <?php echo $foodName; ?>
                </h3>
                <table>
                    <tr>
                        <td>
                            <h4>Amount per 100g:</h4>
                        </td>
                    </tr>
                    <?php foreach ($nutritionData as $key => $value) { ?>
                        <?php
                        if (is_array($value)) {
                            foreach ($value as $subKey => $subValue) {
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo ucfirst(str_replace("_", " ", $subKey)) . ': ' . $subValue . '<br>';
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo $value;
                        }
                        ?>
                    <?php } ?>
                </table>
            <?php } ?>
            <button class="button-89" style="display:block; margin:0 auto;"
                onClick="window.location.href = 'dashboard.php'"> Back </button>
        </section>
    </main>
    <footer>
        <p>&copy; 2023 DigitalGym. All rights reserved.</p>
    </footer>
</body>

</html>