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

$productosAgrupados = [];

if (!isset($_SESSION['saskia']) || empty($_SESSION['saskia'])) {
    $carritoVacio = true;
} else {
    $carritoVacio = false;
    foreach ($_SESSION['saskia'] as $item) {
        if (!isset($item['izena'])) {
            $item['izena'] = $item['mota'] . ' ' . $item['marka']; 
        }

        $clave = $item['izena'];
        if (!isset($productosAgrupados[$clave])) {
            $productosAgrupados[$clave] = $item;
            $productosAgrupados[$clave]['cantidad'] = 1;
        } else {
            $productosAgrupados[$clave]['cantidad']++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations['Zure Saskia']; ?></title>
    <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
<header>
    <h1><?php echo $translations['Zure Saskia']; ?></h1>
    <div class="menu-icon">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>
    <nav>
        <ul>
            <li><a href="index.php"><?php echo $translations['Hasiera']; ?></a></li>
        </ul>
    </nav>
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
</header>
<main>
    <section>
        <h2><?php echo $translations['Saskian dauden produktuak']; ?></h2>
        <?php if ($carritoVacio): ?>
            <p><?php echo $translations['Saskia hutsik dago']; ?></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th><?php echo $translations['Argazkia']; ?></th>
                        <th><?php echo $translations['Izena']; ?></th>
                        <th><?php echo $translations['Kopurua']; ?></th>
                        <th><?php echo $translations['Prezioa']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalGeneral = 0;
                    foreach ($productosAgrupados as $producto):
                        $subtotal = floatval($producto['prezioa']) * $producto['cantidad'];
                        $totalGeneral += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($producto['argazkia_URL'])): ?>
                                <img src="<?php echo htmlspecialchars($producto['argazkia_URL']); ?>" alt="<?php echo htmlspecialchars($producto['izena']); ?>" width="80" height="80">
                            <?php else: ?>
                                <p><?php echo $translations['Argazkia ez dago']; ?></p>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($producto['izena']); ?></td>
                        <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                        <td><?php echo number_format($subtotal, 2, ',', '.'); ?>€</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right;"><?php echo $translations['Guztira']; ?>:</td>
                        <td><?php echo number_format($totalGeneral, 2, ',', '.'); ?>€</td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
        <form method="POST">
            <button class="garbitu-btn" id="garbituBotoia" type="submit" name="garbitu"><?php echo $translations['Saskia Garbitu']; ?></button>
            <button class="erosi-btn" id="erosiBotoia" type="submit" name="erosi"><?php echo $translations['Erosi']; ?></button>
        </form>
    </section>
</main>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['garbitu'])) {
    $_SESSION['saskia'] = [];
    echo "<script>window.location.href = 'zesta.php';</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['erosi'])) {
    if (!empty($_SESSION['saskia'])) {
        $erabiltzailea = $_SESSION['erabiltzailea'];
        $queryErabiltzaile = "SELECT ID FROM erabiltzaileak WHERE Erabiltzailea = ?";
        $stmt = $conn->prepare($queryErabiltzaile);
        $stmt->bind_param("s", $erabiltzailea);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $idErabiltzailea = $row['ID'];

        foreach ($productosAgrupados as $producto) {
            $izena = $producto['izena'];
            $queryProduktua = "SELECT ID FROM stock WHERE Izena = ?";
            $stmt = $conn->prepare($queryProduktua);
            $stmt->bind_param("s", $izena);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $idProduktua = $row['ID'];

            $kantitatea = $producto['cantidad'];
            $prezioa = floatval($producto['prezioa']) * $kantitatea;
            $data = date('Y-m-d');

            $queryInsert = "INSERT INTO eskaerak (ID_Erabiltzailea, ID_Produktua, Eskaera_Data, Kantitatea, Prezioa) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($queryInsert);
            $stmt->bind_param("iisid", $idErabiltzailea, $idProduktua, $data, $kantitatea, $prezioa);
            $stmt->execute();
        }

        $_SESSION['saskia'] = [];
        echo "<script>alert('Erosketa arrakastatsua izan da!'); window.location.href = 'zesta.php';</script>";
    }
}
?>
<footer>
    <p>&copy; 2025 Bizikleta Alokairu Denda - Eskubide guztiak erreserbatuta</p>
</footer>
<script src="../public/menu.js"></script>
</body>
</html>