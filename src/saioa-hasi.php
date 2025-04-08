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

$error = ''; 

// LOGIN BLOKEA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['selectedLang'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prestatu SQL para obtener el usuario
        $sql = "SELECT * FROM erabiltzaileak WHERE Erabiltzailea = :Erabiltzailea";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['Erabiltzailea' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            // Comparar la contraseña en texto plano directamente
            if ($password === $user['Pasahitza']) {
                $_SESSION['erabiltzailea'] = $user['Erabiltzailea'];
                $_SESSION['rola'] = $user['Rola'] ?? 'langilea';  // Guardar rol (admin o langilea)
                $_SESSION['saskia'] = [];  
                header("Location: index.php");
                exit();
            } else {
                $error = isset($translations['Erabiltzaile izena edo pasahitza okerrak.']) ? $translations['Erabiltzaile izena edo pasahitza okerrak.'] : 'Error de autenticación.';
            }
        } else {
            $error = isset($translations['Erabiltzaile izena edo pasahitza okerrak.']) ? $translations['Erabiltzaile izena edo pasahitza okerrak.'] : 'Error de autenticación.';
        }
    } else {
        $error = isset($translations['All fields are required.']) ? $translations['All fields are required.'] : 'Por favor, complete ambos campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo trans('Login'); ?></title>
    <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
<header>
    <h1><?php echo trans('PEDALKADA WEBGUNEA'); ?></h1>
    <div class="menu-icon">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>
    <nav>
        <ul>
            <li class="dropdown">
                <a href="#" class="dropbtn"><?php echo trans('Menu'); ?></a>
                <div class="dropdown-content">
                    <a href="kontaktua.php"><?php echo trans('Contact'); ?></a>
                </div>
            </li>
            <li>
                <form method="post">
                    <?php if ($lang == 'eus'): ?>
                        <button type="submit" name="selectedLang" value="en">
                            <div class="language-flag">
                                <img src="../public/uk_flag.png" alt="English" width="50" height="50">
                            </div>
                        </button>
                    <?php else: ?>
                        <button type="submit" name="selectedLang" value="eus">
                            <div class="language-flag">
                                <img src="../public/ikurrina.png" alt="Euskera" width="50" height="50">
                            </div>
                        </button>
                    <?php endif; ?>
                </form>
            </li>
        </ul>
    </nav>
</header>

<main style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div style="max-width: 400px; width: 100%;">
        <h2><?php echo trans('Login'); ?></h2>
        <?php if (!empty($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <form action="saioa-hasi.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            <label for="username" style="font-size: 1.2em;"><?php echo trans('Username'); ?>:</label>
            <input type="text" id="username" name="username" required style="height: 2em; font-size: 1.1em;">
            
            <label for="password" style="font-size: 1.2em;"><?php echo trans('Password'); ?>:</label>
            <input type="password" id="password" name="password" required style="height: 2em; font-size: 1.1em;">
            
            <button type="submit" style="font-size: 1.2em;"><?php echo trans('Login'); ?></button>
        </form>
        <p><?php echo trans('Already have an account?'); ?> <a href="erregistroa.php"><?php echo trans('Login here'); ?></a></p>
        <p><a href="index.php"><?php echo trans('Back to homepage'); ?></a></p>
    </div>
</main>

<footer>
    <p>&copy; PEDALKADA - <?php echo trans('Eskubide gustiak erreserbatuta'); ?></p>
</footer>
<script src="menu.js"></script>
</body>
</html>
