<?php
session_set_cookie_params(0);
session_start();
include 'dbKonexioa.php'; 

define('APP_DIR', __DIR__);

require_once APP_DIR . '/itzulpenak/translations.php';

if (isset($_POST['selectedLang'])) {
    $valid_languages = ['eus', 'en'];
    $lang = in_array($_POST['selectedLang'], $valid_languages) ? $_POST['selectedLang'] : 'eus';
    $_SESSION["_LANGUAGE"] = $lang;
} else {
    $lang = $_SESSION["_LANGUAGE"] ?? 'eus';
}

$translations = require __DIR__ . "/itzulpenak/" . $lang . ".php";

if (!isset($_SESSION['saskia'])) {
    $_SESSION['saskia'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gehitu'])) {
    if (!isset($_SESSION['erabiltzailea'])) {
        header("Location: saioa-hasi.php");
        exit();
    }
    $mota = $_POST['mota'];
    $marka = $_POST['marka'];
    $eredua = $_POST['eredua'];
    $kolorea = $_POST['kolorea'];
    $prezioa = $_POST['prezioa'];
    $argazkia = $_POST['argazkia_URL'];

    $_SESSION['saskia'][] = [
    'izena' => $mota . ' ' . $marka, 
    'mota' => $mota,
    'marka' => $marka,
    'eredua' => $eredua,
    'kolorea' => $kolorea,
    'prezioa' => $prezioa,
    'argazkia_URL' => $argazkia
];
}

$bilatu = isset($_GET['bilatu']) ? $_GET['bilatu'] : '';
$filtratu = isset($_GET['filtratu']) ? $_GET['filtratu'] : '';

$sql = "SELECT * FROM bizikleta WHERE mota LIKE ? OR marka LIKE ? OR eredua LIKE ? OR kolorea LIKE ?";
$params = ["%$bilatu%", "%$bilatu%", "%$bilatu%", "%$bilatu%"];

if ($filtratu == 'asc') {
    $sql .= " ORDER BY prezioa ASC";
} elseif ($filtratu == 'desc') {
    $sql .= " ORDER BY prezioa DESC";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", ...$params);
$stmt->execute();
$emaitza = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo trans('PEDALKADA WEBGUNEA'); ?></title>
    <link rel="stylesheet" href="../public/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
                            <button type="submit" id="hizkuntza-ikonoa" name="selectedLang" value="en">
                                <div class="language-flag">
                                    <img src="../public/uk_flag.png" id="hikuntza-ikonoa" alt="English" width="50" height="50">
                                </div>
                            </button>
                        <?php else: ?>
                            <button type="submit" id="hizkuntza-ikonoa" name="selectedLang" value="eus">
                                <div class="language-flag">
                                    <img src="../public/ikurrina.png" id="hizkuntza-ikonoa" alt="Euskera" width="50" height="50">
                                </div>
                            </button>
                        <?php endif; ?>
                    </form>
                </li>

                <li>
                    <a href="saioa-hasi.php">
                        <img src="../public/login_icon.jpg" id="login-ikonoa" alt="Saioa Hasi" width="50" height="50">
                    </a>
                    <?php if (isset($_SESSION['erabiltzaileak'])): ?>
                        <span class="erabiltzaileak"><?php echo str_replace("{usuario}", htmlspecialchars($_SESSION['erabiltzaileak']), trans('Ongi etorri, {usuario}!')); ?></span>
                    <?php endif; ?>
                </li>

                <?php if (isset($_SESSION['erabiltzaileak'])): ?>
                <li>
                    <a href="saioa-itxi.php" class="logout-button"><?php echo trans('Saioa Itxi'); ?></a>
                </li>
                <?php endif; ?>

                <li>
                    <a href="zesta.php">
                        <img src="../public/carrito.jpg" id="saskia-ikonoa" alt="Saskia" width="50" height="50">
                    </a>
                    <span id="saskia-kopurua"><?php echo count($_SESSION['saskia']); ?></span>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2><?php echo trans('Bizikletak'); ?></h2>
            <form method="GET" action="index.php">
                <label for="bilatu"><?php echo trans('Bilatu'); ?>:</label>
                <input type="text" id="bilatu" name="bilatu" placeholder="<?php echo trans('Mota, marka, eredua edo kolorea'); ?>" value="<?php echo htmlspecialchars($bilatu); ?>">

                <label for="filtratu"><?php echo trans('Filtratu prezioaren arabera'); ?>:</label>
                <select id="filtratu" name="filtratu">
                    <option value=""><?php echo trans('Aukeratu'); ?></option>
                    <option value="asc" <?php if ($filtratu == 'asc') echo 'selected'; ?>><?php echo trans('Prezioa: Txikienetik handienera'); ?></option>
                    <option value="desc" <?php if ($filtratu == 'desc') echo 'selected'; ?>><?php echo trans('Prezioa: Handienetik txikienera'); ?></option>
                </select>

                <button id="Bilatu" type="submit"><?php echo trans('Bilatu'); ?></button>
            </form>

            <div id="produktuak">
                <?php
                if ($emaitza->num_rows > 0) {
                    while ($row = $emaitza->fetch_assoc()) {
                        echo "<div class='produktua'>";
                        echo "<h3>" . htmlspecialchars($row['mota']) . " - " . htmlspecialchars($row['marka']) . "</h3>";
                        echo "<img src='" . htmlspecialchars($row['argazkia_URL']) . "' alt='" . htmlspecialchars($row['mota']) . "' width='200' height='200'>";
                        echo "<p>" . trans('Eredua') . ": " . htmlspecialchars($row['eredua']) . "</p>";
                        echo "<p>" . trans('Kolorea') . ": " . htmlspecialchars($row['kolorea']) . "</p>";
                        echo "<p>" . trans('Egoera') . ": " . htmlspecialchars($row['egoera']) . "</p>";
                        echo "<p>" . trans('Prezioa') . ": " . htmlspecialchars($row['prezioa']) . "€</p>";

                        echo "<form method='POST' action='index.php'>
                                <input type='hidden' name='mota' value='" . htmlspecialchars($row['mota']) . "'>
                                <input type='hidden' name='marka' value='" . htmlspecialchars($row['marka']) . "'>
                                <input type='hidden' name='eredua' value='" . htmlspecialchars($row['eredua']) . "'>
                                <input type='hidden' name='kolorea' value='" . htmlspecialchars($row['kolorea']) . "'>
                                <input type='hidden' name='prezioa' value='" . htmlspecialchars($row['prezioa']) . "'>
                                <input type='hidden' name='argazkia_URL' value='" . htmlspecialchars($row['argazkia_URL']) . "'>
                                <button type='submit' id='gehituSaskira' name='gehitu'>" . trans('Gehitu Saskira') . "</button>
                              </form>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>" . trans('Ez dago bizikletarik zure irizpideekin.') . "</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; BIZIKLETA DENDAREN WEBGUNEA - <?php echo trans('Eskubide gustiak erreserbatuta'); ?></p>
    </footer>
    <script src="menu.js"></script>
</body>
</html>

<?php
$conn->close();
?>