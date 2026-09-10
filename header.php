<?php
require_once __DIR__ . '/config.php';
$page_title = $page_title ?? 'H-DELLAYA';
$body_class = $body_class ?? 'public';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#FDF7F9">
<title><?= e($page_title) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= url('style.css') ?>">
<?php if ($body_class === 'admin'): ?><link rel="stylesheet" href="<?= url('admin.css') ?>"><?php endif; ?>
</head>
<body class="page-<?= e($body_class) ?>">
<?php include __DIR__ . '/nav.php'; ?>