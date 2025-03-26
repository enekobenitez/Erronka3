<?php
session_start(); 

if (!isset($_SESSION['saskia'])) {
    $_SESSION['saskia'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $izena = $_POST['izena'] ?? '';
    $argazkia = $_POST['argazkia'] ?? '';
    $prezioa = $_POST['prezioa'] ?? '';

    if (!empty($izena) && !empty($prezioa)) {
        
        $_SESSION['saskia'][] = [
            'Izena' => $izena,
            'Argazkia_URL' => $argazkia,
            'Prezioa' => $prezioa
        ];
        echo "Produktua saskira gehitu da!";
    } else {
        echo "Errorea: datuak falta dira!";
    }
}
?>
