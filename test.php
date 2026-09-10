<?php
echo "<h1>🔍 Diagnostic H-DELLAYA</h1><pre>";

echo "PHP version : " . phpversion() . "\n\n";

echo "Extensions :\n";
echo "  pdo_mysql : " . (extension_loaded('pdo_mysql') ? "✅ OK" : "❌ MANQUANT") . "\n";
echo "  mysqli    : " . (extension_loaded('mysqli') ? "✅ OK" : "❌ MANQUANT") . "\n\n";

echo "Variables d'environnement :\n";
echo "  DB_HOST : " . (getenv('DB_HOST') ?: 'NON DÉFINI') . "\n";
echo "  DB_PORT : " . (getenv('DB_PORT') ?: 'NON DÉFINI') . "\n";
echo "  DB_NAME : " . (getenv('DB_NAME') ?: 'NON DÉFINI') . "\n";
echo "  DB_USER : " . (getenv('DB_USER') ?: 'NON DÉFINI') . "\n";
echo "  DB_PASS : " . (getenv('DB_PASS') ? "(défini, " . strlen(getenv('DB_PASS')) . " caractères)" : 'NON DÉFINI') . "\n\n";

echo "Test connexion MySQL...\n";
try {
    $dsn = 'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), $options);
    echo "  ✅ Connexion réussie !\n\n";

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables dans la base :\n";
    if (empty($tables)) {
        echo "  ⚠️ Aucune table — il faut importer database.sql\n";
    } else {
        foreach ($tables as $t) echo "  ✅ $t\n";
    }
} catch (Exception $e) {
    echo "  ❌ Erreur : " . $e->getMessage() . "\n";
}

echo "</pre>";
