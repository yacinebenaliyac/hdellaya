<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/cloudinary.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h1>🔍 Test Upload Cloudinary</h1><pre>";

echo "Variables d'environnement :\n";
echo "  CLOUDINARY_CLOUD_NAME : " . (getenv('CLOUDINARY_CLOUD_NAME') ?: '❌ NON DÉFINI') . "\n";
echo "  CLOUDINARY_API_KEY    : " . (getenv('CLOUDINARY_API_KEY') ? '(défini, ' . strlen(getenv('CLOUDINARY_API_KEY')) . ' caractères)' : '❌ NON DÉFINI') . "\n";
echo "  CLOUDINARY_API_SECRET : " . (getenv('CLOUDINARY_API_SECRET') ? '(défini, ' . strlen(getenv('CLOUDINARY_API_SECRET')) . ' caractères)' : '❌ NON DÉFINI') . "\n\n";

echo "Test connexion Cloudinary (sans upload) :\n";
$cloudName = getenv('CLOUDINARY_CLOUD_NAME');
$apiKey    = getenv('CLOUDINARY_API_KEY');
$apiSecret = getenv('CLOUDINARY_API_SECRET');

if (!$cloudName || !$apiKey || !$apiSecret) {
    die("❌ Identifiants manquants sur Render !\n");
}

// Test simple : ping l'API Cloudinary
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.cloudinary.com/v1_1/{$cloudName}/ping",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Erreur : $error\n";
} elseif ($httpCode === 200) {
    echo "✅ API Cloudinary accessible !\n";
    echo "Réponse : $response\n\n";
} else {
    echo "⚠ HTTP $httpCode\n";
    echo "Réponse : $response\n";
}

echo "\nTest signature :\n";
$timestamp = time();
$params = ['folder' => 'hdellaya', 'timestamp' => $timestamp];
ksort($params);
$toSign = '';
foreach ($params as $k => $v) $toSign .= "$k=$v&";
$toSign = rtrim($toSign, '&') . $apiSecret;
$sig = sha1($toSign);
echo "Signature générée : $sig\n";
echo "Timestamp : $timestamp\n";

echo "\n</pre>";

// Formulaire d'upload test
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['test_image']['tmp_name'])) {
    echo "<h2>Résultat de l'upload :</h2><pre>";
    $result = cloudinary_upload($_FILES['test_image']['tmp_name'], 'hdellaya');
    if ($result['ok']) {
        echo "✅ Upload réussi !\n\n";
        echo "URL : " . $result['url'] . "\n\n";
        echo "Clique pour voir : <a href='" . $result['url'] . "' target='_blank'>" . $result['url'] . "</a>\n";
    } else {
        echo "❌ Échec : " . $result['error'] . "\n";
    }
    echo "</pre>";
}

echo '<hr><h2>Test upload image</h2>';
echo '<form method="post" enctype="multipart/form-data">';
echo '<input type="file" name="test_image" accept="image/*" required>';
echo '<button type="submit" style="padding:10px 20px;margin-left:10px">Uploader</button>';
echo '</form>';
