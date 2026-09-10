<?php
require_once __DIR__ . '/auth.php';
require_login();

/* ---------- Actions ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id    = (int)($_POST['id'] ?? 0);
        $cat   = (int)($_POST['category_id'] ?? 0) ?: null;
        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $price = (float)str_replace([' ', ','], ['', '.'], $_POST['price'] ?? '0');
        $feat  = !empty($_POST['featured']) ? 1 : 0;
        $img   = trim($_POST['current_image'] ?? '');

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp','gif'], true) && $_FILES['image']['size'] <= 4*1024*1024) {
                $fname = 'p_' . bin2hex(random_bytes(8)) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . '/' . $fname)) {
                    if ($img && file_exists(ROOT_DIR . '/' . $img) && strpos($img, 'uploads/') === 0) @unlink(ROOT_DIR . '/' . $img);
                    $img = UPLOAD_URL . '/' . $fname;
                }
            }
        }
        if (!empty($_POST['remove_image'])) {
            if ($img && file_exists(ROOT_DIR . '/' . $img) && strpos($img, 'uploads/') === 0) @unlink(ROOT_DIR . '/' . $img);
            $img = '';
        }

        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE products SET category_id=?, name=?, description=?, price=?, image=?, featured=? WHERE id=?');
            $stmt->execute([$cat, $name, $desc, $price, $img, $feat, $id]);
            flash('ok', 'Produit mis à jour.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO products (category_id, name, description, price, image, featured) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$cat, $name, $desc, $price, $img, $feat]);
            flash('ok', 'Produit ajouté.');
        }
        redirect(url('products.php'));
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare('SELECT image FROM products WHERE id=?');
        $stmt->execute([$id]);
        $img = $stmt->fetchColumn();
        if ($img && file_exists(ROOT_DIR . '/' . $img) && strpos($img, 'uploads/') === 0) @unlink(ROOT_DIR . '/' . $img);
        $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
        flash('ok', 'Produit supprimé.');
        redirect(url('products.php'));
    }

    if ($action === 'toggle_featured') {
        $id = (int)$_POST['id'];
        $pdo->prepare('UPDATE products SET featured = 1 - featured WHERE id=?')->execute([$id]);
        redirect(url('products.php'));
    }
}

/* ---------- Édition ---------- */
$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY position, id')->fetchAll();
$products   = $pdo->query(
    'SELECT p.*, c.name AS cat_name FROM products p
     LEFT JOIN categories c ON c.id = p.category_id
     ORDER BY p.id DESC'
)->fetchAll();

$page_title = 'Produits — Admin';
$body_class = 'admin';
include __DIR__ . '/header.php';
?>
<main class="admin-main">
  <div class="admin-container">

    <div class="page-head">
      <div>
        <span class="page-tag">Gestion</span>
        <h1>Produits <span class="count"><?= count($products) ?></span></h1>
      </div>
    </div>

    <?php if ($m = flash('ok')): ?><div class="flash ok">✅ <?= e($m) ?></div><?php endif; ?>

    <div class="panel">
      <div class="panel-head">
        <h2><?= $edit ? '✏️ Modifier le produit' : '➕ Ajouter un produit' ?></h2>
        <?php if ($edit): ?><a href="<?= url('products.php') ?>" class="btn-ghost">Annuler</a><?php endif; ?>
      </div>
      <form method="post" enctype="multipart/form-data" class="form-grid">
        <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
        <input type="hidden" name="current_image" value="<?= e($edit['image'] ?? '') ?>">

        <div class="form-row">
          <label>Nom du produit *
            <input type="text" name="name" required value="<?= e($edit['name'] ?? '') ?>" placeholder="Ex: Bougie de henné prestige">
          </label>
          <label>Catégorie
            <select name="category_id">
              <option value="">— Aucune —</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= ((int)($edit['category_id'] ?? 0) === (int)$c['id']) ? 'selected' : '' ?>>
                  <?= e($c['emoji']) ?> <?= e($c['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>

        <div class="form-row">
          <label>Prix (DA) *
            <input type="text" name="price" required placeholder="2500" value="<?= e($edit['price'] ?? '') ?>">
          </label>
          <label class="check-label">
            <input type="checkbox" name="featured" value="1" <?= !empty($edit['featured']) ? 'checked' : '' ?>>
            <span>★ Marquer comme coup de cœur</span>
          </label>
        </div>

        <label>Description
          <textarea name="description" rows="3" placeholder="Matières, finitions, usage..."><?= e($edit['description'] ?? '') ?></textarea>
        </label>

        <label>Photo du produit
          <input type="file" name="image" accept="image/*">
          <small class="hint">JPG, PNG, WEBP ou GIF · max 4 Mo</small>
        </label>

        <?php if (!empty($edit['image']) && file_exists(ROOT_DIR . '/' . $edit['image'])): ?>
          <div class="img-preview">
            <img src="<?= url($edit['image']) ?>" alt="">
            <label class="inline"><input type="checkbox" name="remove_image" value="1"> Supprimer cette image</label>
          </div>
        <?php endif; ?>

        <div class="form-actions">
          <button type="submit" class="btn-primary">
            <?= $edit ? '💾 Mettre à jour' : '✨ Ajouter le produit' ?>
          </button>
        </div>
      </form>
    </div>

    <div class="panel">
      <div class="panel-head"><h2>Tous les produits</h2></div>
      <?php if (!$products): ?>
        <p class="muted">Aucun produit. Ajoute le premier ci-dessus.</p>
      <?php else: ?>
        <div class="prod-grid">
          <?php foreach ($products as $p): ?>
            <div class="prod-card <?= !empty($p['featured']) ? 'is-featured' : '' ?>">
              <div class="prod-thumb">
                <?php if (!empty($p['image']) && file_exists(ROOT_DIR . '/' . $p['image'])): ?>
                  <img src="<?= url($p['image']) ?>" alt="">
                <?php else: ?>
                  <div class="no-img">🌿 Pas de photo</div>
                <?php endif; ?>
                <?php if (!empty($p['featured'])): ?><span class="star-badge">★</span><?php endif; ?>
              </div>
              <div class="prod-info">
                <div class="prod-cat"><?= e($p['cat_name'] ?? '—') ?></div>
                <b><?= e($p['name']) ?></b>
                <div class="prod-price"><?= money($p['price']) ?></div>
              </div>
              <div class="prod-actions">
                <a href="?edit=<?= (int)$p['id'] ?>" class="btn-mini">✏️ Modifier</a>
                <form method="post" style="display:inline">
                  <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
                  <input type="hidden" name="action" value="toggle_featured">
                  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                  <button class="btn-mini" type="submit" title="Coup de cœur"><?= !empty($p['featured']) ? '★' : '☆' ?></button>
                </form>
                <form method="post" style="display:inline" onsubmit="return confirm('Supprimer ce produit ?');">
                  <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                  <button class="btn-mini danger" type="submit">🗑</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>
<?php include __DIR__ . '/footer.php'; ?>