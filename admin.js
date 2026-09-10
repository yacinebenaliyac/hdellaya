/* ============================================================
   H-DELLAYA — Script admin
   ============================================================ */
(function () {
  'use strict';

  /* Auto-masquer les flashes */
  document.querySelectorAll('.flash').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .5s, transform .5s';
      el.style.opacity = '0';
      el.style.transform = 'translateY(-8px)';
      setTimeout(function () { el.style.display = 'none'; }, 500);
    }, 4000);
  });

  /* Preview d'image locale avant upload */
  var fileInput = document.querySelector('input[type=file][name=image]');
  if (fileInput) {
    fileInput.addEventListener('change', function () {
      var file = this.files && this.files[0];
      if (!file) return;
      var wrap = document.querySelector('.img-preview');
      if (!wrap) {
        wrap = document.createElement('div');
        wrap.className = 'img-preview';
        fileInput.parentNode.appendChild(wrap);
      }
      var oldImg = wrap.querySelector('img');
      if (oldImg) oldImg.parentNode.removeChild(oldImg);
      var img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      wrap.insertBefore(img, wrap.firstChild);
    });
  }

  /* Menu mobile admin */
  var mt = document.getElementById('menuToggle');
  var nl = document.getElementById('navLinks');
  if (mt && nl) {
    mt.addEventListener('click', function () { nl.classList.toggle('open'); });
  }

})();