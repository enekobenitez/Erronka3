<?php
$servername = "localhost:3306";
$username = "root";
$password = "1WMG2023";
$dbname = "erronka3";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Konexio errorea " . $conn->connect_error);
}

try {
   
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Errorea PDO: " . $e->getMessage());
}
?>
