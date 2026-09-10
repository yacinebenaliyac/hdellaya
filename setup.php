<?php
header('Content-Type: text/html; charset=utf-8');
echo "<h1>🌱 Installation H-DELLAYA</h1><pre>";

try {
    $dsn = 'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8mb4';
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ]);

    echo "✅ Connexion OK\n\n";

    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        die("❌ database.sql introuvable. Vérifie qu'il est bien sur GitHub.");
    }

    $sql = file_get_contents($sqlFile);
    $sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
    $sql = preg_replace('/USE\s+`?\w+`?\s*;/i', '', $sql);
    $sql = preg_replace('/SET\s+FOREIGN_KEY_CHECKS\s*=\s*\d+;/i', '', $sql);

    $pdo->exec($sql);
    echo "🎉 Import réussi !\n\n";

    $products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $settings = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();

    echo "Tables créées :\n";
    echo "  ✅ products   : $products lignes\n";
    echo "  ✅ categories : $categories lignes\n";
    echo "  ✅ orders     : $orders lignes\n";
    echo "  ✅ settings   : $settings lignes\n\n";

    echo "🌐 Ton site est prêt : <a href='index.php'>Aller au site</a>\n";
    echo "🔐 Admin : <a href='login.php'>Se connecter à l'admin</a>\n";
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
echo "</pre>";
