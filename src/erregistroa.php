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
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
    $birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';

    if (empty($name) || empty($lastname) || empty($birthdate) || empty($address)) {
        $error = $translations['Datu guztiak beharrezkoak dira.'];
    } elseif ($password !== $confirmPassword) {
        $error = $translations['Pasahitzak ez dira bat etorri.'];
    } else {
        $sql = "SELECT * FROM erabiltzaileak WHERE Erabiltzailea = :Erabiltzailea";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['Erabiltzailea' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            $error = $translations['Izen hau erabiltzen hari da.'];
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO erabiltzaileak (Erabiltzailea, Pasahitza, Izena, Abizena, Jaiotze_Eguna, Helbidea) 
                    VALUES (:Erabiltzailea, :Pasahitza, :Izena, :Abizena, :Jaiotze_Eguna, :Helbidea)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'Erabiltzailea' => $username,
                'Pasahitza' => $hashedPassword,
                'Izena' => $name,
                'Abizena' => $lastname,
                'Jaiotze_Eguna' => $birthdate,
                'Helbidea' => $address,
            ]);

            $_SESSION['erabiltzailea'] = $username;
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations['Erregistroa']; ?></title>
    <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
<header>
    <h1><?php echo $translations['PEDALKADA']; ?></h1>
    <nav>
        <ul>
            <li class="dropdown">
                <a href="#" class="dropbtn"><?php echo $translations['Menu']; ?></a>
                <div class="dropdown-content">
                    <a href="kontaktua.php"><?php echo $translations['Contact']; ?></a>
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

<h2><?php echo $translations['Erregistroa']; ?></h2>

<?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

<form action="erregistroa.php" method="POST">
    <label for="name"><?php echo $translations['Izena']; ?>:</label>
    <input type="text" id="name" name="name" required><br>

    <label for="lastname"><?php echo $translations['Abizena']; ?>:</label>
    <input type="text" id="lastname" name="lastname" required><br>

    <label for="birthdate"><?php echo $translations['Jaiotze Eguna']; ?>:</label>
    <input type="date" id="birthdate" name="birthdate" required><br>

    <label for="address"><?php echo $translations['Helbidea']; ?>:</label>
    <input type="text" id="address" name="address" required><br>

    <label for="username"><?php echo $translations['Erabiltzaile izena']; ?>:</label>
    <input type="text" id="username" name="username" required><br>
    
    <label for="password"><?php echo $translations['Pasahitza']; ?>:</label>
    <input type="password" id="password" name="password" required><br>

    <label for="confirmPassword"><?php echo $translations['Berriro sartu pasahitza']; ?>:</label>
    <input type="password" id="confirmPassword" name="confirmPassword" required><br>
    
    <button type="submit"><?php echo $translations['Erregistratu']; ?></button>
</form>

<p><?php echo $translations['Kontua ahal daukezu?']; ?> <a href="saioa-hasi.php"><?php echo $translations['Hemen login egin']; ?></a></p>
<p><a href="index.php"><?php echo $translations['Itzuli hasierako orrira']; ?></a></p>

</body>
</html>
