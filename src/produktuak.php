<?php
include 'dbKonexioa.php';

$sql = "SELECT * FROM bizikleta";
$emaitza = $conn->query($sql);

if ($emaitza->num_rows > 0) {
    while ($row = $emaitza->fetch_assoc()) {
        echo "<div class='produktua'>";
        echo "<h3>" . htmlspecialchars($row['mota']) . "</h3>";
        echo "<h3>" . htmlspecialchars($row['marka']) . "</h3>";
        echo "<h3>" . htmlspecialchars($row['eredua']) . "</h3>";
        echo "<img src='" . htmlspecialchars($row['argazkia_URL']) . "' alt='" . htmlspecialchars($row['Izena']) . "' width='200' height='200'>";
        echo "<p>Prezioa: " . htmlspecialchars($row['prezioa']) . "€</p>";
        
        echo "<form method='POST' action='index.php'>
                <input type='hidden' name='mota' value='" . htmlspecialchars($row['mota']) . "'>
                <input type='hidden' name='marka' value='" . htmlspecialchars($row['marka']) . "'>
                <input type='hidden' name='marka' value='" . htmlspecialchars($row['eredua']) . "'>
                <input type='hidden' name='prezioa' value='" . htmlspecialchars($row['prezioa']) . "'>
                <input type='hidden' name='argazkia_URL' value='" . htmlspecialchars($row['argazkia_URL']) . "'>
                <button type='submit' name='gehitu'>Gehitu Saskira</button>
              </form>";
        echo "</div>";
    }
} else {
    echo "<p>Ez dago produkturik.</p>";
}

$conn->close();
?>