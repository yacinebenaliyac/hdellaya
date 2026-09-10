<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/* ---------- Enregistrement commande (AJAX) ---------- */
if (($_GET['action'] ?? '') === 'save_order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $in = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    $itemsArr = $in['items'] ?? [];
    $itemsTxt = '';
    $total    = 0;
    if (is_array($itemsArr)) {
        foreach ($itemsArr as $it) {
            $itemsTxt .= '• ' . ($it['name'] ?? '') . ' (' . ($it['price'] ?? 0) . " DA)\n";
            $total += (float)($it['price'] ?? 0);
        }
    }
    $stmt = $pdo->prepare(
        'INSERT INTO orders (name, phone, address, items, notes, total)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        trim($in['nom']     ?? ''),
        trim($in['tel']     ?? ''),
        trim($in['adresse'] ?? ''),
        trim($itemsTxt),
        trim($in['notes']   ?? ''),
        $total,
    ]);
    echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

/* ---------- Données ---------- */
$settings = [];
foreach ($pdo->query('SELECT `key`, `value` FROM settings')->fetchAll() as $r) {
    $settings[$r['key']] = $r['value'];
}
$categories = $pdo->query('SELECT * FROM categories ORDER BY position, id')->fetchAll();
$products   = $pdo->query(
    'SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, c.emoji AS cat_emoji
     FROM products p LEFT JOIN categories c ON c.id = p.category_id
     ORDER BY p.featured DESC, p.id DESC'
)->fetchAll();

$page_title = 'H-DELLAYA — Bougies artisanales · Tlemcen';
$body_class = 'public';
include __DIR__ . '/header.php';
?>

<!-- Bandeau annonce -->
<?php if (!empty($settings['announce'])): ?>
<div class="announce-bar">✨ <?= e($settings['announce']) ?> ✨</div>
<?php endif; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <div class="hero-text" data-reveal>
      <div class="kicker"><?= e($settings['hero_kicker']) ?></div>
      <h1><?= hero_title_html($settings['hero_title']) ?></h1>
      <p class="lead"><?= e($settings['hero_lead']) ?></p>
      <div class="hero-ctas">
        <a href="#catalogue" class="btn btn-primary">Voir les créations →</a>
        <a href="<?= e($settings['instagram']) ?>" target="_blank" rel="noopener" class="btn btn-ghost">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          Instagram
        </a>
      </div>
      <div class="tags">
        <span class="tag">✦ Fait main</span>
        <span class="tag">✦ Sur commande</span>
        <span class="tag">✦ Tlemcen & environs</span>
      </div>
    </div>
    <div class="hero-visual" data-reveal>
      <div class="glow"></div>
      <div class="glow glow-2"></div>
      <svg class="candle-svg" viewBox="0 0 200 380">
        <defs>
          <linearGradient id="wax" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#FFF9FB"/>
            <stop offset="100%" stop-color="#FBE0EA"/>
          </linearGradient>
          <radialGradient id="fire" cx="50%" cy="60%">
            <stop offset="0%" stop-color="#FFF3C4"/>
            <stop offset="55%" stop-color="#E89CC4"/>
            <stop offset="100%" stop-color="#D97BA6"/>
          </radialGradient>
        </defs>
        <g class="flame-flicker">
          <path d="M100 40c10 26-16 32-16 54a16 16 0 0032 0c0-12-6-18-6-30 10 8 16 22 16 34a26 26 0 01-52 0c0-28 16-38 26-58z" fill="url(#fire)"/>
          <ellipse cx="100" cy="96" rx="6" ry="10" fill="#FFF8E1" opacity="0.95"/>
        </g>
        <rect x="98" y="112" width="4" height="14" fill="#3B2A33"/>
        <rect x="60" y="124" width="80" height="230" rx="8" fill="url(#wax)" stroke="#F1E1E8" stroke-width="1.5"/>
        <rect x="60" y="124" width="80" height="16" fill="#FBE0EA" rx="8"/>
        <path d="M60 160c10 4 70 4 80 0v180c-10 4-70 4-80 0z" fill="#FDF0F5" opacity="0.7"/>
        <circle cx="100" cy="240" r="22" fill="none" stroke="#E091B0" stroke-width="2" opacity="0.6"/>
        <text x="100" y="245" font-family="Fraunces, serif" font-size="12" fill="#B84D81" text-anchor="middle" opacity="0.75">HD</text>
      </svg>
    </div>
  </div>
</section>

<!-- CATALOGUE -->
<section id="catalogue" class="section">
  <div class="container">
    <div class="section-head" data-reveal>
      <div>
        <span class="section-tag">Nos créations</span>
        <h2>Un menu pensé pour vos <em>plus beaux moments</em></h2>
      </div>
      <p>Chaque pièce est réalisée à la main dans nos teintes ivoire, rose poudré et dorée.</p>
    </div>

    <div class="filters" data-reveal>
      <button class="filter-btn active" data-filter="tout">Tout voir</button>
      <?php foreach ($categories as $c): ?>
        <button class="filter-btn" data-filter="<?= e($c['slug']) ?>">
          <?= e($c['emoji']) ?> <?= e($c['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <div class="catalogue-grid" id="catalogueGrid">
      <?php foreach ($products as $p): ?>
        <article class="card"
                 data-cat="<?= e($p['cat_slug'] ?? '') ?>"
                 data-id="<?= (int)$p['id'] ?>"
                 data-name="<?= e($p['name']) ?>"
                 data-price="<?= (float)$p['price'] ?>"
                 data-desc="<?= e($p['description']) ?>"
                 data-img="<?= e($p['image'] ?? '') ?>"
                 data-catname="<?= e($p['cat_name'] ?? '') ?>">
          <div class="card-media">
            <?php if (!empty($p['image'])): ?>
              <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
            <?php else: ?>
              <div class="card-placeholder">
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 2c1 4-3 5-3 9a3 3 0 006 0c0-2-1-3-1-5 2 1 3 4 3 6a5 5 0 01-10 0c0-5 3-6 5-10z" fill="#C75F92"/></svg>
              </div>
            <?php endif; ?>
            <?php if (!empty($p['featured'])): ?><span class="badge-featured">★ Coup de cœur</span><?php endif; ?>
            <div class="card-quick">
              <button class="quick-view" title="Aperçu rapide">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="card-cat"><?= e($p['cat_emoji'] ?? '') ?> <?= e($p['cat_name'] ?? '') ?></div>
            <h3><?= e($p['name']) ?></h3>
            <p><?= e($p['description']) ?></p>
            <div class="card-foot">
              <span class="price"><?= money($p['price']) ?></span>
              <button class="add-btn" data-action="add">+ Ajouter</button>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <?php if (!$products): ?>
      <p class="empty-state">Aucun produit pour l'instant. Ajoute ton premier article depuis l'admin ✨</p>
    <?php endif; ?>
  </div>
</section>

<!-- INSTAGRAM -->
<section id="instagram" class="section section-alt">
  <div class="container">
    <div class="section-head" data-reveal>
      <div>
        <span class="section-tag">Instagram</span>
        <h2>Sur notre <em>fil</em></h2>
      </div>
      <p>Nos dernières créations, coulisses de l'atelier et retours de clientes.</p>
    </div>
    <div class="ig-stats" data-reveal>
      <div class="stat"><b><?= e($settings['ig_posts']) ?></b><span>publications</span></div>
      <div class="stat"><b><?= e($settings['ig_followers']) ?></b><span>abonnés</span></div>
      <div class="stat"><b><?= e($settings['ig_following']) ?></b><span>suivi(e)s</span></div>
    </div>
    <div class="ig-grid" data-reveal>
      <div class="ig-tile"><span>🕯️</span><p>Shmou3 el 3arous</p></div>
      <div class="ig-tile"><span>🌸</span><p>Faveurs de mariage</p></div>
      <div class="ig-tile"><span>✨</span><p>Coulisses atelier</p></div>
      <div class="ig-tile"><span>💌</span><p>Vos retours</p></div>
    </div>
    <div class="ig-cta" data-reveal>
      <a href="<?= e($settings['instagram']) ?>" target="_blank" rel="noopener" class="btn btn-primary">@h_dellaya →</a>
    </div>
  </div>
</section>

<!-- AVIS -->
<section id="avis" class="section">
  <div class="container">
    <div class="section-head" data-reveal>
      <div>
        <span class="section-tag">Avis clients</span>
        <h2>Elles nous font <em>confiance</em></h2>
      </div>
      <p>Ce que nos clientes disent après réception de leur commande.</p>
    </div>
    <div class="reviews-grid">
      <div class="review" data-reveal>
        <div class="stars">★★★★★</div>
        <p>« Bougie de henné magnifique, exactement comme sur les photos. Livrée à temps pour la soirée. »</p>
        <div class="who">— Amina · Tlemcen</div>
      </div>
      <div class="review" data-reveal>
        <div class="stars">★★★★★</div>
        <p>« Le bouquet en cire est superbe et tient très bien la forme. Très bon rapport qualité-prix. »</p>
        <div class="who">— Yasmine · Maghnia</div>
      </div>
      <div class="review" data-reveal>
        <div class="stars">★★★★★</div>
        <p>« Contact rapide sur Instagram, elle a été à l'écoute de mes demandes de personnalisation. »</p>
        <div class="who">— Nour · Oran</div>
      </div>
    </div>
  </div>
</section>

<!-- COMMANDER -->
<section id="commander" class="section">
  <div class="container">
    <div class="order-shell" data-reveal>
      <div class="order-side">
        <span class="section-tag">Commander</span>
        <h2>Passer commande</h2>
        <p>Ajoute tes créations favorites, remplis le formulaire, et l'application SMS s'ouvre avec tout prêt à envoyer.</p>
        <ul class="order-info-list">
          <li><span>📍</span> <?= e($settings['location']) ?></li>
          <li><span>📞</span> <a href="tel:<?= e($settings['phone']) ?>"><?= e($settings['phone']) ?></a></li>
          <li><span>💬</span> Commande possible aussi par DM Instagram</li>
        </ul>
      </div>
      <form id="orderForm" class="order-form"
            data-phone="<?= e($settings['phone_intl']) ?>">
        <div class="field-group">
          <div class="field">
            <label for="nom">Nom & prénom *</label>
            <input id="nom" name="nom" type="text" placeholder="Votre nom" required>
          </div>
          <div class="field">
            <label for="tel">Téléphone *</label>
            <input id="tel" name="tel" type="tel" placeholder="06 XX XX XX XX" required>
          </div>
        </div>
        <div class="field">
          <label for="adresse">Adresse de livraison *</label>
          <input id="adresse" name="adresse" type="text" placeholder="Quartier, ville" required>
        </div>
        <div class="field">
          <label>Créations sélectionnées <small>(modifiable)</small></label>
          <div id="quotePreview" class="quote-preview empty">
            <span class="quote-empty">Clique sur <b>+ Ajouter</b> sur une création ci-dessus, ou remplis juste tes coordonnées.</span>
          </div>
        </div>
        <div class="field">
          <label for="notes">Précisions (optionnel)</label>
          <textarea id="notes" name="notes" rows="3" placeholder="Couleurs, date de l'événement, quantité..."></textarea>
        </div>
        <button type="submit" class="submit-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
          Envoyer la demande par SMS
        </button>
        <div class="confirm-msg" id="confirmMsg">✅ Ouverture de l'application SMS…</div>
      </form>
    </div>
  </div>
</section>

<!-- Bouton panier flottant -->
<button id="quoteBubble" class="quote-bubble" aria-label="Ma sélection">
  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
  <span id="quoteCount">0</span>
</button>

<!-- Modal produit -->
<div id="productModal" class="modal">
  <div class="modal-backdrop" data-close></div>
  <div class="modal-content">
    <button class="modal-close" data-close aria-label="Fermer">×</button>
    <div class="modal-media" id="modalMedia"></div>
    <div class="modal-body">
      <div class="card-cat" id="modalCat"></div>
      <h2 id="modalName"></h2>
      <p id="modalDesc"></p>
      <div class="modal-foot">
        <span class="price" id="modalPrice"></span>
        <button class="btn btn-primary" id="modalAdd">+ Ajouter à ma sélection</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="toast"></div>

<?php include __DIR__ . '/footer.php'; ?>
