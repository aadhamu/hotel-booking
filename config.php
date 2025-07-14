<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

 
$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$db_name = getenv("DB_NAME");

$conn = new mysqli($host, $user, $pass, $db_name, 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

