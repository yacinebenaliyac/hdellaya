<?php
// Active TOUTES les erreurs pour voir ce qui plante
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 Debug H-DELLAYA</h1><hr>";
echo "<h2>Chargement de config.php</h2>";
require_once __DIR__ . '/config.php';
echo "✅ config.php chargé sans erreur<br><br>";

echo "<h2>Chargement de db.php</h2>";
require_once __DIR__ . '/db.php';
echo "✅ db.php chargé sans erreur<br><br>";

echo "<h2>Test requête simple</h2>";
$n = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
echo "✅ $n produits trouvés<br><br>";

echo "<h2>Chargement de index.php</h2>";
echo "<hr>";

// Capture tout ce qui va sortir d'index.php
ob_start();
try {
    include __DIR__ . '/index.php';
    $output = ob_get_clean();
    echo "✅ index.php exécuté sans erreur fatale<br><br>";
    echo "<h3>Début du rendu (1000 premiers caractères) :</h3>";
    echo "<pre>" . htmlspecialchars(substr($output, 0, 1000)) . "</pre>";
} catch (Throwable $e) {
    ob_end_clean();
    echo "❌ <b>Erreur dans index.php :</b><br>";
    echo "<pre style='background:#fee;padding:20px;border-radius:8px'>";
    echo "Message : " . htmlspecialchars($e->getMessage()) . "\n";
    echo "Fichier : " . htmlspecialchars($e->getFile()) . "\n";
    echo "Ligne   : " . $e->getLine() . "\n\n";
    echo "Trace :\n" . htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}
