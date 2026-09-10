<?php
require_once __DIR__ . '/auth.php';
admin_logout();
redirect(url('login.php'));