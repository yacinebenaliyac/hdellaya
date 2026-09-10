# 🌸 H-DELLAYA — Site vitrine + Admin

Site web artisanal avec panneau d'administration complet.

## 📋 Prérequis
- PHP 7.4+ (PHP 8 recommandé)
- MySQL / MariaDB
- Un hébergeur PHP

## 🚀 Installation

### 1. Upload
Uploade **tout le contenu du dossier** `hdellaya/` à la racine de ton hébergement
(ou dans un sous-dossier, ça marche aussi — l'URL s'adapte automatiquement).

### 2. Base de données
1. Crée une base `hdellaya` (charset `utf8mb4_unicode_ci`).
2. Importe `database.sql` (onglet **Importer** dans phpMyAdmin).

### 3. Configuration (`config.php`)
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hdellaya');
define('DB_USER', 'ton_user');
define('DB_PASS', 'ton_mdp');

define('ADMIN_USERNAME', 'admin');       // ⚠ à changer
define('ADMIN_PASSWORD', 'dellaya2024'); // ⚠ à changer