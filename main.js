/* ============================================================
   H-DELLAYA — Script public (dynamique)
   ============================================================ */
(function () {
  'use strict';
  var QUOTE_KEY = 'hdellaya_quote_v1';

  /* ---------- Utilitaires ---------- */
  function $(s, r) { return (r || document).querySelector(s); }
  function $$(s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); }
  function money(v) { return new Intl.NumberFormat('fr-FR').format(v) + ' DA'; }

  function toast(msg) {
    var t = $('#toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(toast._t);
    toast._t = setTimeout(function () { t.classList.remove('show'); }, 2200);
  }

  /* ---------- Menu mobile ---------- */
  var mt = $('#menuToggle'), nl = $('#navLinks');
  if (mt && nl) {
    mt.addEventListener('click', function () { nl.classList.toggle('open'); });
    nl.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') nl.classList.remove('open');
    });
  }

  /* ---------- Reveal au scroll ---------- */
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) {
          en.target.classList.add('revealed');
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.12 });
    $$('[data-reveal]').forEach(function (el) { io.observe(el); });
  } else {
    $$('[data-reveal]').forEach(function (el) { el.classList.add('revealed'); });
  }

  /* ---------- Filtres animés ---------- */
  var cards = $$('.card');
  $$('.filter-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      $$('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var f = btn.dataset.filter;
      cards.forEach(function (c, i) {
        var show = (f === 'tout' || c.dataset.cat === f);
        if (show) {
          c.classList.remove('hidden');
          c.style.animation = 'none';
          void c.offsetHeight;
          c.style.animation = 'cardIn .4s ease ' + (i * 30) + 'ms backwards';
        } else {
          c.classList.add('hidden');
        }
      });
    });
  });

  /* ---------- Quote (sélection) ---------- */
  function getQuote() {
    try { return JSON.parse(localStorage.getItem(QUOTE_KEY)) || []; }
    catch (e) { return []; }
  }
  function saveQuote(q) {
    try { localStorage.setItem(QUOTE_KEY, JSON.stringify(q)); } catch (e) {}
    renderQuote();
  }
  function addToQuote(item) {
    var q = getQuote();
    if (q.some(function (x) { return x.id === item.id; })) {
      toast('Déjà dans ta sélection ✨');
      return;
    }
    q.push(item);
    saveQuote(q);
    toast(item.name + ' ajouté ✨');
    var bubble = $('#quoteBubble');
    if (bubble) { bubble.classList.add('bump'); setTimeout(function () { bubble.classList.remove('bump'); }, 400); }
  }
  function removeFromQuote(id) {
    saveQuote(getQuote().filter(function (x) { return x.id !== id; }));
  }

  function renderQuote() {
    var q = getQuote();
    var cnt = $('#quoteCount');
    var bubble = $('#quoteBubble');
    var prev = $('#quotePreview');

    if (cnt) cnt.textContent = q.length;
    if (bubble) bubble.classList.toggle('has-items', q.length > 0);

    if (!prev) return;

    if (q.length === 0) {
      prev.classList.add('empty');
      prev.innerHTML = '<span class="quote-empty">Clique sur <b>+ Ajouter</b> sur une création ci-dessus, ou remplis juste tes coordonnées.</span>';
      return;
    }

    prev.classList.remove('empty');
    var total = 0;
    var html = q.map(function (it) {
      total += Number(it.price) || 0;
      return '<div class="quote-item">' +
               '<span>' + esc(it.name) + '</span>' +
               '<span><b>' + money(it.price) + '</b> ' +
               '<button type="button" data-remove="' + esc(it.id) + '">✕</button></span>' +
             '</div>';
    }).join('');
    html += '<div class="quote-total"><span>Total estimé</span><span>' + money(total) + '</span></div>';
    prev.innerHTML = html;

    $$('button[data-remove]', prev).forEach(function (b) {
      b.addEventListener('click', function () { removeFromQuote(b.dataset.remove); });
    });
  }

  function esc(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c];
    });
  }

  /* ---------- Clic "+ Ajouter" sur les cartes ---------- */
  cards.forEach(function (card) {
    var btn = $('.add-btn', card);
    if (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        addToQuote(cardToItem(card));
        btn.classList.add('added');
        btn.textContent = '✓ Ajouté';
        setTimeout(function () {
          btn.classList.remove('added');
          btn.textContent = '+ Ajouter';
        }, 1800);
      });
    }
  });

  function cardToItem(card) {
    return {
      id:    card.dataset.id,
      name:  card.dataset.name,
      price: Number(card.dataset.price) || 0,
      img:   card.dataset.img || ''
    };
  }

  /* ---------- Modal produit ---------- */
  var modal = $('#productModal');
  var modalItem = null;

  function openModal(card) {
    if (!modal) return;
    modalItem = cardToItem(card);
    $('#modalName').textContent  = card.dataset.name;
    $('#modalDesc').textContent  = card.dataset.desc;
    $('#modalPrice').textContent = money(card.dataset.price);
    $('#modalCat').textContent   = card.dataset.catname || '';

    var media = $('#modalMedia');
    if (card.dataset.img) {
      media.innerHTML = '<img src="' + esc(card.dataset.img) + '" alt="">';
    } else {
      media.innerHTML = '<svg viewBox="0 0 24 24" fill="none"><path d="M12 2c1 4-3 5-3 9a3 3 0 006 0c0-2-1-3-1-5 2 1 3 4 3 6a5 5 0 01-10 0c0-5 3-6 5-10z" fill="#C75F92"/></svg>';
    }

    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeModal() {
    if (!modal) return;
    modal.classList.remove('open');
    document.body.style.overflow = '';
  }

  cards.forEach(function (card) {
    var qv = $('.quick-view', card);
    var media = $('.card-media', card);
    if (qv) qv.addEventListener('click', function (e) { e.stopPropagation(); openModal(card); });
    if (media) media.addEventListener('click', function () { openModal(card); });
  });

  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target.hasAttribute('data-close')) closeModal();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeModal();
    });
    var mAdd = $('#modalAdd');
    if (mAdd) mAdd.addEventListener('click', function () {
      if (modalItem) addToQuote(modalItem);
      closeModal();
    });
  }

  /* ---------- Bubble panier ---------- */
  var bubble = $('#quoteBubble');
  if (bubble) bubble.addEventListener('click', function () {
    var target = document.getElementById('commander');
    if (target) target.scrollIntoView({ behavior: 'smooth' });
  });

  /* ---------- Formulaire commande → SMS ---------- */
  var form = $('#orderForm');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var items = getQuote();

      var payload = {
        nom:     form.nom.value.trim(),
        tel:     form.tel.value.trim(),
        adresse: form.adresse.value.trim(),
        notes:   form.notes.value.trim(),
        items:   items
      };

      fetch('index.php?action=save_order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      }).catch(function () {});

      var msg = $('#confirmMsg');
      if (msg) msg.classList.add('show');

      var parts = ['Bonjour H-DELLAYA, je souhaite commander :', ''];
      parts.push('Nom : ' + payload.nom);
      parts.push('Téléphone : ' + payload.tel);
      parts.push('Adresse : ' + payload.adresse);
      if (items.length) {
        parts.push('');
        parts.push('Créations :');
        var total = 0;
        items.forEach(function (it) {
          parts.push('  • ' + it.name + ' — ' + money(it.price));
          total += it.price;
        });
        parts.push('');
        parts.push('Total estimé : ' + money(total));
      }
      if (payload.notes) {
        parts.push('');
        parts.push('Précisions : ' + payload.notes);
      }
      parts.push('');
      parts.push('Merci !');

      var body = parts.join('\n');
      var phone = form.dataset.phone || '';
      var smsUrl = 'sms:' + phone + '?body=' + encodeURIComponent(body);

      setTimeout(function () { window.location.href = smsUrl; }, 300);

      saveQuote([]);
      form.reset();
      setTimeout(function () { if (msg) msg.classList.remove('show'); }, 4000);
    });
  }

  /* ---------- Init ---------- */
  renderQuote();

})();