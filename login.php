<?php
require_once __DIR__ . '/auth.php';
if (is_logged_in()) redirect(url('dashboard.php'));

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (admin_login($_POST['username'] ?? '', $_POST['password'] ?? '')) {
        flash('ok', 'Bienvenue !');
        redirect(url('dashboard.php'));
    }
    $error = 'Identifiants incorrects.';
}
$page_title = 'Connexion — H-DELLAYA';
$body_class = 'login';
include __DIR__ . '/header.php';
?>
<main class="login-wrap">
  <form method="post" class="login-box" autocomplete="off">
    <div class="login-logo">
      <svg viewBox="0 0 24 24" fill="none" width="36" height="36"><path d="M12 2c1 4-3 5-3 9a3 3 0 006 0c0-2-1-3-1-5 2 1 3 4 3 6a5 5 0 01-10 0c0-5 3-6 5-10z" fill="#C75F92"/></svg>
    </div>
    <h1>H-DELLAYA</h1>
    <p>Espace administrateur</p>
    <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
    <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
    <label>Nom d'utilisateur
      <input type="text" name="username" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
    </label>
    <label>Mot de passe
      <input type="password" name="password" required>
    </label>
    <button type="submit">Se connecter</button>
    <a href="<?= url('index.php') ?>" class="back">← Retour au site</a>
  </form>
</main>
<?php include __DIR__ . '/footer.php'; ?>