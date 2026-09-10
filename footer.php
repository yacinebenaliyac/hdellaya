<?php require_once __DIR__ . '/config.php'; ?>
<footer class="site-footer">
  <div class="foot-inner">
    <div class="brand">H-DELLAYA</div>
    <div class="foot-links">
      <a href="https://www.instagram.com/h_dellaya" target="_blank" rel="noopener">Instagram</a>
      <a href="https://www.tiktok.com/@h_dellaya" target="_blank" rel="noopener">TikTok</a>
      <a href="tel:0696352703">0696352703</a>
      <span>Tlemcen, Algérie</span>
    </div>
  </div>
</footer>

<script src="<?= url('main.js') ?>" defer></script>
<?php if (($body_class ?? '') === 'admin'): ?>
<script src="<?= url('admin.js') ?>" defer></script>
<?php endif; ?>
</body>
</html>