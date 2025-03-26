<?php
session_start(); 
include 'dbKonexioa.php'; 

if (!defined('APP_DIR')) {
    define('APP_DIR', __DIR__);  
}
  
require_once APP_DIR . '/itzulpenak/translations.php';
  
if (isset($_POST['selectedLang'])) {
    $valid_languages = ['eus', 'en'];
    $lang = in_array($_POST['selectedLang'], $valid_languages) ? $_POST['selectedLang'] : 'eus'; 
    $_SESSION["_LANGUAGE"] = $lang; 
} else {
    $lang = $_SESSION["_LANGUAGE"] ?? 'eus';
}
  
$translations = require __DIR__ . "/itzulpenak/" . $lang . ".php"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enpresaIzena = $_POST['enpresaIzena'];
    $produktua = $_POST['produktua'];
    $produktuarenDeskripzioa = $_POST['produktuarenDeskripzioa'];
    $dohaintzarenEguna = $_POST['dohaintzarenEguna'];

    if (empty($enpresaIzena) || empty($produktua) || empty($produktuarenDeskripzioa) || empty($dohaintzarenEguna)) {
        $error = $translations['Datu guztiak beharrezkoak dira.'];
    } else {
        $sql = "INSERT INTO enpresa (izena, helbidea, telefonoa, posta_helbidea) 
                VALUES (:izena, :helbidea, :telefonoa, :posta_helbidea)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'izena' => $enpresaIzena,
            'Produktua' => $produktua,
            'Produktuaren_Deskripzioa' => $produktuarenDeskripzioa,
            'Dohaintzaren_Eguna' => $dohaintzarenEguna
        ]);
        
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations['Hornitzaileak Kudeatu']; ?></title>
    <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
    <header>
        <h1><?php echo $translations['Hornitzaileak']; ?></h1>
        <div class="menu-icon">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <nav>
            <ul>
                <li><a href="index.php"><?php echo $translations['Hasiera']; ?></a></li>
                <li><a href="index.php"><?php echo $translations['Produktuak']; ?></a></li>
                <li><a href="logout.php"><?php echo $translations['Log Out']; ?></a></li>
            </ul>
        </nav>
    </header>
    <main style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
        <div style="max-width: 400px; width: 100%;">
            <h2><?php echo $translations['Hornitzailea Gehitu']; ?></h2>
            <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            <form method="POST" action="hornitzailea.php" style="display: flex; flex-direction: column; gap: 15px;">
                <label for="enpresaIzena" style="font-size: 1.2em;"><?php echo $translations['Enpresa Izena']; ?>:</label>
                <input type="text" name="enpresaIzena" required style="height: 2em; font-size: 1.1em;">
                
                <label for="produktua" style="font-size: 1.2em;"><?php echo $translations['Produktua']; ?>:</label>
                <input type="text" name="produktua" required style="height: 2em; font-size: 1.1em;">
                
                <label for="produktuarenDeskripzioa" style="font-size: 1.2em;"><?php echo $translations['Produktuaren Deskripzioa']; ?>:</label>
                <input type="text" name="produktuarenDeskripzioa" required style="height: 2em; font-size: 1.1em;">
                
                <label for="dohaintzarenEguna" style="font-size: 1.2em;"><?php echo $translations['Dohaintzaren Eguna']; ?>:</label>
                <input type="date" name="dohaintzarenEguna" required style="height: 2em; font-size: 1.1em;">
                
                <button type="submit" id="gehituBotoia" style="font-size: 1.2em;"><?php echo $translations['Gehitu']; ?></button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; PEDALKADA - <?php echo $translations['Eskubide gustiak erreserbatuta']; ?></p>
    </footer>
    <script src="menu.js"></script>
</body>
</html>