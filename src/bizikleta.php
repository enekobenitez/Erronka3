<?php
session_start();
include 'dbKonexioa.php';

define('APP_DIR', __DIR__);
require_once APP_DIR . '/itzulpenak/translations.php';

$lang = $_SESSION["_LANGUAGE"] ?? 'eus';
$translations = require __DIR__ . "/itzulpenak/" . $lang . ".php";

if (!isset($_GET['id_bizikleta'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id_bizikleta'];
$stmt = $conn->prepare("SELECT * FROM bizikleta WHERE id_bizikleta = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>" . trans('Bizikleta ez da aurkitu.') . "</p>";
    exit();
}

$bizikleta = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo trans('Bizikleta Xehetasunak'); ?></title>
    <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
    <header>
        <h1><?php echo trans('Bizikleta Xehetasunak'); ?></h1>
        <a href="index.php">&larr; <?php echo trans('Atzera'); ?></a>
    </header>
    <main>
        <div class='produktua'>
            <h2><?php echo htmlspecialchars($bizikleta['mota']) . " - " . htmlspecialchars($bizikleta['marka']); ?></h2>
            <img src='<?php echo htmlspecialchars($bizikleta['argazkia_URL']); ?>' alt='<?php echo htmlspecialchars($bizikleta['mota']); ?>' width='300' height='300'>
            <p><strong><?php echo trans('Eredua'); ?>:</strong> <?php echo htmlspecialchars($bizikleta['eredua']); ?></p>
            <p><strong><?php echo trans('Kolorea'); ?>:</strong> <?php echo htmlspecialchars($bizikleta['kolorea']); ?></p>
            <p><strong><?php echo trans('Egoera'); ?>:</strong> <?php echo htmlspecialchars($bizikleta['egoera']); ?></p>
            <p><strong><?php echo trans('Prezioa'); ?>:</strong> <?php echo htmlspecialchars($bizikleta['prezioa']); ?>€</p>
        </div>
    </main>
    <footer>
        <p>&copy; PEDALKADA - <?php echo trans('Eskubide gustiak erreserbatuta'); ?></p>
    </footer>
</body>
</html>