<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h1>🔍 Diagnostic Products</h1><pre>";

// Vérifie le contenu de products.php
$content = file_get_contents(__DIR__ . '/products.php');

echo "Vérifications de products.php :\n\n";

if (strpos($content, 'cloudinary.php') !== false) {
    echo "  ✅ Ligne 'require cloudinary.php' présente\n";
} else {
    echo "  ❌ Ligne 'require cloudinary.php' ABSENTE — le fichier n'a pas été mis à jour !\n";
}

if (strpos($content, 'cloudinary_upload') !== false) {
    echo "  ✅ Appel cloudinary_upload() présent\n";
} else {
    echo "  ❌ Appel cloudinary_upload() ABSENT — remplace bien le bloc move_uploaded_file !\n";
}

if (strpos($content, 'move_uploaded_file') !== false) {
    echo "  ⚠ Le vieux code 'move_uploaded_file' existe encore\n";
} else {
    echo "  ✅ Vieux code supprimé\n";
}

echo "\nContenu de la base de données :\n";
$stmt = $pdo->query("SELECT id, name, image FROM products ORDER BY id DESC LIMIT 10");
foreach ($stmt->fetchAll() as $p) {
    echo "  ID {$p['id']} : {$p['name']}\n";
    echo "    image = " . ($p['image'] ?: '(vide)') . "\n";
    if ($p['image'] && strpos($p['image'], 'cloudinary.com') !== false) {
        echo "    ✅ URL Cloudinary\n";
    }
}

echo "\n</pre>";
