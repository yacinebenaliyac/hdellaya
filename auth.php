<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function admin_login($u, $p) {
    if (hash_equals(ADMIN_USERNAME, (string)$u) && hash_equals(ADMIN_PASSWORD, (string)$p)) {
        $_SESSION[ADMIN_KEY] = ['u' => ADMIN_USERNAME, 't' => time()];
        return true;
    }
    return false;
}
function admin_logout() { unset($_SESSION[ADMIN_KEY]); }