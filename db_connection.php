<?php
$mysqli = new mysqli("localhost", "root", "", "users_gym");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

return $mysqli; // Return the MySQLi connection object
?>