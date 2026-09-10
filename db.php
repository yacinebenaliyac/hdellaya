<?php
require_once __DIR__ . '/config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 30,
    ];
    if (DB_HOST !== 'localhost' && DB_HOST !== '127.0.0.1') {
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        $options[PDO::MYSQL_ATTR_SSL_CA] = null;
        $dsn .= ';ssl-mode=REQUIRED';
    }
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $ex) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Erreur</title>';
    echo '<style>body{font-family:system-ui;max-width:560px;margin:80px auto;padding:32px;';
    echo 'background:#FDF7F9;border:1px solid #F1E1E8;border-radius:16px;color:#3B2A33}';
    echo 'h1{color:#B84D81;margin-top:0}code{background:#FCE7EF;padding:2px 6px;border-radius:4px}</style>';
    echo '</head><body><h1>⚠ Connexion à la base impossible</h1>';
    echo '<p>Erreur : ' . htmlspecialchars($ex->getMessage()) . '</p>';
    echo '</body></html>';
    exit;
}
