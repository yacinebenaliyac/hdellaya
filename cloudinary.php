<?php
/**
 * Upload vers Cloudinary (sans SDK, via API HTTP)
 */
function cloudinary_upload($filePath, $folder = 'hdellaya') {
    $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
    $apiKey    = getenv('CLOUDINARY_API_KEY');
    $apiSecret = getenv('CLOUDINARY_API_SECRET');

    if (!$cloudName || !$apiKey || !$apiSecret) {
        return ['ok' => false, 'error' => 'Identifiants Cloudinary manquants'];
    }

    $timestamp = time();
    $params = [
        'folder'    => $folder,
        'timestamp' => $timestamp,
    ];

    ksort($params);
    $toSign = '';
    foreach ($params as $k => $v) $toSign .= $k . '=' . $v . '&';
    $toSign = rtrim($toSign, '&') . $apiSecret;
    $signature = sha1($toSign);

    $post = [
        'file'      => new CURLFile($filePath),
        'api_key'   => $apiKey,
        'timestamp' => $timestamp,
        'folder'    => $folder,
        'signature' => $signature,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload",
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $post,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) return ['ok' => false, 'error' => 'cURL: ' . $error];

    $data = json_decode($response, true);
    if ($httpCode !== 200 || !isset($data['secure_url'])) {
        $msg = $data['error']['message'] ?? 'Réponse invalide';
        return ['ok' => false, 'error' => "Cloudinary ($httpCode) : $msg"];
    }

    return [
        'ok'  => true,
        'url' => $data['secure_url'],
    ];
}
