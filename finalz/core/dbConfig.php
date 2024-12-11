<?php  
// dbconfig.php

$host = "localhost";
$user = "root";
$password = "";
$dbname = "finals";
$dsn = "mysql:host={$host};dbname={$dbname}";

try {
    // Create the PDO instance and assign it to $db
    $db = new PDO($dsn, $user, $password);
    $db->exec("SET time_zone = '+08:00';");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
