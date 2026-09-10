<?php
/* ============================================================
   H-DELLAYA — Configuration
   ============================================================ */
if (session_status() === PHP_SESSION_NONE) session_start();

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'hdellaya');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('ADMIN_USERNAME', getenv('ADMIN_USERNAME') ?: 'admin');
define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'dellaya2024');

define('ADMIN_KEY', 'hdellaya_admin');
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('UPLOAD_URL', 'uploads');

date_default_timezone_set('Africa/Algiers');

/* ---------- BASE_URL auto (racine ou sous-dossier) ---------- */
$sn = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
$dir = rtrim(dirname($sn), '/');
if ($dir === '/' || $dir === '.') $dir = '';
define('BASE_URL', $dir);
define('ROOT_DIR', __DIR__);

if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);

/* ---------- Helpers ---------- */
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect($u) { header('Location: ' . $u); exit; }
function url($p = '') { return BASE_URL . '/' . ltrim($p, '/'); }
function is_logged_in() { return !empty($_SESSION[ADMIN_KEY]); }
function require_login() { if (!is_logged_in()) redirect(url('login.php')); }
function flash($k, $v = null) {
    if ($v === null) { $x = $_SESSION['flash'][$k] ?? null; unset($_SESSION['flash'][$k]); return $x; }
    $_SESSION['flash'][$k] = $v;
}
function money($v) { return number_format((float)$v, 0, ',', ' ') . ' DA'; }
function csrf() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_csrf'] ?? '')) {
        http_response_code(419); die('Token de sécurité invalide.');
    }
}
function hero_title_html($t) {
    return preg_replace('/\*(.+?)\*/', '<em>$1</em>', e($t));
}
function status_label($s) {
    return ['nouveau'=>'Nouveau','en_cours'=>'En cours','livre'=>'Livré','annule'=>'Annulé'][$s] ?? $s;
}
