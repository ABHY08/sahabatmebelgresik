// ============================================================
//  Sahabat Mebel — App JS v3.0 (Modern Redesign)
// ============================================================

const CART_KEY  = 'sahabatmebel_cart';
const WA_NUMBER = '6281234567890';

let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];

// ── Utilities ──────────────────────────────────────────────

function saveCart() { localStorage.setItem(CART_KEY, JSON.stringify(cart)); }

function toast(msg, type = 'success') {
  const container = document.getElementById('toastContainer');
  if (!container) return;
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.innerHTML = `<span>${type === 'success' ? '✓' : '✕'}</span> ${msg}`;
  container.appendChild(el);
  requestAnimationFrame(() => {
    requestAnimationFrame(() => el.classList.add('show'));
  });
  setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 400); }, 3200);
}

function formatPrice(n) {
  return 'Rp ' + Number(n).toLocaleString('id-ID', { minimumFractionDigits: 0 });
}

// ── Navbar ────────────────────────────────────────────────

const navbar    = document.getElementById('navbar');
const hamburger = document.getElementById('hamburger');
const navMenu   = document.getElementById('navMenu');

let lastScrollY = window.scrollY;
let ticking = false;

window.addEventListener('scroll', () => {
  if (!ticking) {
    requestAnimationFrame(() => {
      const y = window.scrollY;
      navbar?.classList.toggle('scrolled', y > 20);
      ticking = false;
      lastScrollY = y;
    });
    ticking = true;
  }
}, { passive: true });

hamburger?.addEventListener('click', () => {
  const isOpen = navMenu.classList.toggle('open');
  hamburger.classList.toggle('open', isOpen);
});

document.querySelectorAll('.nav-menu a').forEach(link => {
  link.addEventListener('click', () => {
    navMenu?.classList.remove('open');
    hamburger?.classList.remove('open');
  });
});

// Active link on scroll
const sections = document.querySelectorAll('section[id]');
const navLinks  = document.querySelectorAll('.nav-menu a');
window.addEventListener('scroll', () => {
  let current = '';
  sections.forEach(s => {
    if (window.scrollY >= s.offsetTop - 140) current = s.id;
  });
  navLinks.forEach(a => {
    a.classList.remove('active');
    const href = a.getAttribute('href');
    if (href && href === '#' + current) a.classList.add('active');
  });
}, { passive: true });

// ── Product Filter ─────────────────────────────────────────

const filterBtns   = document.querySelectorAll('.filter-btn');
const productCards = document.querySelectorAll('.product-card');

filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    filterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.dataset.filter;
    let delay = 0;
    productCards.forEach(card => {
      const show = filter === 'all' || card.dataset.category === filter;
      if (show) {
        card.style.display = '';
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
          card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, delay);
        delay += 50;
      } else {
        card.style.display = 'none';
      }
    });
  });
});

// ── Room Ideas Filter ──────────────────────────────────────

const roomFilterBtns = document.querySelectorAll('.room-filter-btn');
const roomCards      = document.querySelectorAll('.room-card');

roomFilterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    roomFilterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const room = btn.dataset.room;
    let delay = 0;
    let firstVisible = true;
    roomCards.forEach(card => {
      const show = room === 'all' || card.dataset.room === room;
      if (show) {
        card.style.display = '';
        card.style.opacity = '0';
        card.style.transform = 'translateY(16px)';
        // Make the first visible card span 2 columns on 'all' view
        if (room === 'all' && firstVisible) {
          card.classList.add('room-card-featured');
        } else {
          card.classList.remove('room-card-featured');
        }
        firstVisible = false;
        setTimeout(() => {
          card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, delay);
        delay += 70;
      } else {
        card.style.display = 'none';
        card.classList.remove('room-card-featured');
      }
    });
  });
});

// ── Cart ───────────────────────────────────────────────────

function renderCart() {
  const itemsEl  = document.getElementById('cartItems');
  const footerEl = document.getElementById('cartFooter');
  const countEl  = document.getElementById('cartCount');
  const totalEl  = document.getElementById('cartTotal');

  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
  const count = cart.reduce((s, i) => s + i.qty, 0);
  if (countEl) countEl.textContent = count;

  if (cart.length === 0) {
    if (itemsEl) itemsEl.innerHTML = `
      <div class="cart-empty">
        <div class="cart-empty-icon">🪑</div>
        <p>Keranjang masih kosong.<br>Yuk mulai belanja!</p>
      </div>`;
    if (footerEl) footerEl.style.display = 'none';
    return;
  }

  if (footerEl) footerEl.style.display = '';
  if (totalEl) totalEl.textContent = formatPrice(total);

  if (itemsEl) {
    itemsEl.innerHTML = cart.map((item, idx) => `
      <div class="cart-item">
        <img class="cart-item-img" src="${item.img}" alt="${item.name}"
             onerror="this.src='assets/images/placeholder.jpg'">
        <div class="cart-item-info">
          <div class="cart-item-name">${item.name}</div>
          <div class="cart-item-price">${formatPrice(item.price)}</div>
          <div class="cart-item-qty">
            <button class="qty-btn" onclick="changeQty(${idx},-1)">−</button>
            <span>${item.qty}</span>
            <button class="qty-btn" onclick="changeQty(${idx},1)">+</button>
          </div>
        </div>
        <button class="cart-item-remove" onclick="removeItem(${idx})">✕</button>
      </div>
    `).join('');
  }
}

function addToCart(id, name, price, img, qty = 1) {
  const existing = cart.find(i => i.id === id);
  if (existing) { existing.qty += qty; }
  else { cart.push({ id, name, price: parseFloat(price), img, qty }); }
  saveCart();
  renderCart();
  toast(`"${name}" ditambahkan ke keranjang!`);
}

window.changeQty = (idx, delta) => {
  cart[idx].qty += delta;
  if (cart[idx].qty <= 0) cart.splice(idx, 1);
  saveCart(); renderCart();
};

window.removeItem = (idx) => {
  cart.splice(idx, 1);
  saveCart(); renderCart();
};

// Cart Toggle
const cartOverlay = document.getElementById('cartOverlay');
const cartSidebar = document.getElementById('cartSidebar');
const cartToggle  = document.getElementById('cartToggle');
const cartClose   = document.getElementById('cartClose');

function openCart()  {
  cartOverlay?.classList.add('open');
  cartSidebar?.classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeCart() {
  cartOverlay?.classList.remove('open');
  cartSidebar?.classList.remove('open');
  document.body.style.overflow = '';
}

cartToggle?.addEventListener('click', openCart);
cartClose?.addEventListener('click', closeCart);
cartOverlay?.addEventListener('click', closeCart);

// Add to cart buttons
document.querySelectorAll('.add-cart-btn').forEach(btn => {
  btn.addEventListener('click', e => {
    e.stopPropagation();
    addToCart(btn.dataset.id, btn.dataset.name, btn.dataset.price, btn.dataset.img);
    // micro animation
    btn.style.transform = 'scale(0.9)';
    setTimeout(() => btn.style.transform = '', 200);
  });
});

// ── Product Detail Modal ───────────────────────────────────

const productModal      = document.getElementById('productModal');
const productModalClose = document.getElementById('productModalClose');
let detailQty = 1;
let currentDetailProduct = null;

function openProductModal(card) {
  const id         = card.dataset.id;
  const name       = card.querySelector('.product-name')?.textContent || '';
  const priceText  = card.querySelector('.product-price')?.textContent || '';
  const desc       = card.querySelector('.product-desc')?.textContent || '';
  const img        = card.querySelector('.product-img img')?.src || '';
  const category   = card.querySelector('.product-badge')?.textContent || '';
  const material   = card.querySelector('.add-cart-btn')?.dataset.material || '';
  const dimensions = card.querySelector('.add-cart-btn')?.dataset.dimensions || '';
  const rawPrice   = card.querySelector('.add-cart-btn')?.dataset.price || '0';

  detailQty = 1;
  document.getElementById('detailQtyVal').textContent = 1;
  document.getElementById('detailImg').src = img;
  document.getElementById('detailCategory').textContent = category;
  document.getElementById('detailName').textContent = name;
  document.getElementById('detailPrice').textContent = priceText;
  document.getElementById('detailDesc').textContent = desc;

  const specBody = document.querySelector('#detailSpecTable tbody');
  if (specBody) {
    const specs = [
      ['Nama Produk', name],
      ['Kategori', category],
      material   ? ['Bahan / Material', material]   : null,
      dimensions ? ['Dimensi / Ukuran', dimensions] : null,
      ['Garansi', '2 Tahun'],
      ['Custom Tersedia', 'Ya — hubungi WA untuk detail'],
      ['Pengiriman', 'Gratis area Jabodetabek'],
    ].filter(Boolean);

    specBody.innerHTML = specs.map(([label, val]) => `
      <tr><td>${label}</td><td>${val}</td></tr>
    `).join('');
  }

  const waMsg = encodeURIComponent(`Halo Sahabat Mebel, saya tertarik dengan produk "${name}". Bisa info lebih lanjut?`);
  const waBtn = document.getElementById('detailWaBtn');
  if (waBtn) waBtn.href = `https://wa.me/${WA_NUMBER}?text=${waMsg}`;

  currentDetailProduct = { id, name, price: rawPrice, img };

  document.querySelectorAll('.detail-tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.detail-tab-content').forEach(c => c.classList.remove('active'));
  document.querySelector('.detail-tab-btn[data-tab="desc"]')?.classList.add('active');
  document.getElementById('tabDesc')?.classList.add('active');

  productModal?.classList.add('open');
  document.body.style.overflow = 'hidden';
}

document.querySelectorAll('.detail-tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.detail-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.detail-tab-content').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    const target = document.getElementById('tab' + btn.dataset.tab.charAt(0).toUpperCase() + btn.dataset.tab.slice(1));
    if (target) target.classList.add('active');
  });
});

productCards.forEach(card => {
  card.addEventListener('click', e => {
    if (!e.target.closest('.add-cart-btn')) openProductModal(card);
  });
});

productModalClose?.addEventListener('click', () => {
  productModal?.classList.remove('open');
  document.body.style.overflow = '';
});
productModal?.addEventListener('click', e => {
  if (e.target === productModal) {
    productModal.classList.remove('open');
    document.body.style.overflow = '';
  }
});

document.getElementById('detailQtyMinus')?.addEventListener('click', () => {
  if (detailQty > 1) { detailQty--; document.getElementById('detailQtyVal').textContent = detailQty; }
});
document.getElementById('detailQtyPlus')?.addEventListener('click', () => {
  detailQty++; document.getElementById('detailQtyVal').textContent = detailQty;
});
document.getElementById('detailAddCart')?.addEventListener('click', () => {
  if (!currentDetailProduct) return;
  addToCart(currentDetailProduct.id, currentDetailProduct.name, currentDetailProduct.price, currentDetailProduct.img, detailQty);
  productModal?.classList.remove('open');
  document.body.style.overflow = '';
  openCart();
});

// ── Testimonials Carousel ──────────────────────────────────

(function initCarousel() {
  const carousel     = document.getElementById('testimonialsCarousel');
  const dotsEl       = document.getElementById('carouselDots');
  const prevBtn      = document.getElementById('carouselPrev');
  const nextBtn      = document.getElementById('carouselNext');
  if (!carousel) return;

  const cards = carousel.querySelectorAll('.testimonial-card');
  if (!cards.length) return;

  function getPerView() {
    if (window.innerWidth <= 768)  return 1;
    if (window.innerWidth <= 1024) return 2;
    return 3;
  }

  let current = 0, autoTimer = null;
  const getMax = () => Math.max(0, cards.length - getPerView());

  function buildDots() {
    if (!dotsEl) return;
    const max = getMax();
    dotsEl.innerHTML = '';
    for (let i = 0; i <= max; i++) {
      const d = document.createElement('div');
      d.className = 'carousel-dot' + (i === current ? ' active' : '');
      d.addEventListener('click', () => goTo(i));
      dotsEl.appendChild(d);
    }
  }

  function goTo(i) {
    const max = getMax();
    current = Math.max(0, Math.min(i, max));
    const cardW = cards[0].offsetWidth + 24;
    carousel.style.transform = `translateX(-${current * cardW}px)`;
    document.querySelectorAll('.carousel-dot').forEach((d, j) => {
      d.classList.toggle('active', j === current);
    });
  }

  function next() { goTo(current + 1 > getMax() ? 0 : current + 1); }
  function prev() { goTo(current - 1 < 0 ? getMax() : current - 1); }

  const startAuto = () => { stopAuto(); autoTimer = setInterval(next, 4800); };
  const stopAuto  = () => { if (autoTimer) { clearInterval(autoTimer); autoTimer = null; } };

  nextBtn?.addEventListener('click', () => { next(); stopAuto(); startAuto(); });
  prevBtn?.addEventListener('click', () => { prev(); stopAuto(); startAuto(); });

  let touchX = 0;
  carousel.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, { passive: true });
  carousel.addEventListener('touchend',   e => {
    const diff = touchX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) { diff > 0 ? next() : prev(); stopAuto(); startAuto(); }
  }, { passive: true });

  carousel.addEventListener('mouseenter', stopAuto);
  carousel.addEventListener('mouseleave', startAuto);

  buildDots(); startAuto();
  window.addEventListener('resize', () => { buildDots(); goTo(0); });
})();

// ── Checkout Modal ─────────────────────────────────────────

const checkoutModal = document.getElementById('checkoutModal');
const modalClose    = document.getElementById('modalClose');
const checkoutBtn   = document.getElementById('checkoutBtn');
const placeOrderBtn = document.getElementById('placeOrderBtn');

checkoutBtn?.addEventListener('click', () => {
  if (!cart.length) { toast('Keranjang masih kosong!', 'error'); return; }
  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
  document.getElementById('orderSummaryBox').innerHTML = `
    ${cart.map(i => `
      <div class="order-summary-item">
        <span>${i.name} × ${i.qty}</span>
        <span>${formatPrice(i.price * i.qty)}</span>
      </div>`).join('')}
    <div class="order-summary-total">
      <span>Total</span><span>${formatPrice(total)}</span>
    </div>`;
  closeCart();
  checkoutModal?.classList.add('open');
  document.body.style.overflow = 'hidden';
});

modalClose?.addEventListener('click', () => {
  checkoutModal?.classList.remove('open');
  document.body.style.overflow = '';
});
checkoutModal?.addEventListener('click', e => {
  if (e.target === checkoutModal) {
    checkoutModal.classList.remove('open');
    document.body.style.overflow = '';
  }
});

placeOrderBtn?.addEventListener('click', async () => {
  const name    = document.getElementById('custName')?.value.trim();
  const address = document.getElementById('custAddress')?.value.trim();
  const wa      = document.getElementById('custWa')?.value.trim();
  const notes   = document.getElementById('custNotes')?.value.trim();

  if (!name || !address || !wa) {
    toast('Mohon isi semua kolom yang wajib diisi.', 'error'); return;
  }

  placeOrderBtn.disabled = true;
  placeOrderBtn.textContent = 'Memproses…';

  try {
    const res  = await fetch('api/place_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, address, whatsapp: wa, notes, items: cart })
    });
    const data = await res.json();

    if (data.success) {
      cart = []; saveCart(); renderCart();
      document.getElementById('modalTitle').textContent = 'Pesanan Berhasil! 🎉';
      document.getElementById('modalBody').innerHTML = `
        <div class="success-screen">
          <div class="success-icon">🎉</div>
          <h3 class="success-title">Terima Kasih!</h3>
          <p class="success-desc">Pesanan Anda telah berhasil dibuat. Tim kami akan segera menghubungi Anda via WhatsApp.</p>
          <div class="order-code">${data.order_code}</div>
          <p class="success-desc" style="font-size:0.84rem">Simpan kode pesanan di atas untuk pelacakan.</p>
          <a href="https://wa.me/${WA_NUMBER}?text=Halo%2C%20saya%20baru%20order%20kode%20${data.order_code}%20atas%20nama%20${encodeURIComponent(name)}"
             target="_blank" class="btn btn-wa" style="margin-top:1rem">
            💬 Konfirmasi via WhatsApp
          </a>
        </div>`;
      document.getElementById('modalFooter').innerHTML = `
        <button class="btn btn-outline w-full" onclick="document.getElementById('checkoutModal').classList.remove('open');document.body.style.overflow='';location.reload()">
          Lanjut Belanja
        </button>`;
    } else {
      toast(data.message || 'Terjadi kesalahan. Coba lagi.', 'error');
    }
  } catch (e) {
    toast('Gagal terhubung ke server. Silakan coba lagi.', 'error');
  }

  placeOrderBtn.disabled = false;
  placeOrderBtn.textContent = 'Buat Pesanan 🛒';
});

// ── Quick Konsultasi Form (NEW) ────────────────────────────

window.submitKonsultasi = function(e) {
  e.preventDefault();
  const nama     = document.getElementById('kNama')?.value.trim();
  const wa       = document.getElementById('kWa')?.value.trim();
  const kebutuhan= document.getElementById('kKebutuhan')?.value;
  const budget   = document.getElementById('kBudget')?.value;

  if (!nama || !wa || !kebutuhan) {
    toast('Mohon isi nama, nomor WA, dan kebutuhan Anda.', 'error'); return;
  }

  let msg = `Halo Sahabat Mebel! 👋\n\nSaya ${nama} ingin konsultasi furnitur.\n\n`;
  msg += `🪑 *Kebutuhan:* ${kebutuhan}\n`;
  if (budget) msg += `💰 *Budget:* ${budget}\n`;
  msg += `\nMohon bantu saya! 🙏`;

  window.open(`https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
};

// ── WhatsApp Floating Button ───────────────────────────────

const waFloatBtn = document.getElementById('waFloatBtn');
waFloatBtn?.addEventListener('click', () => {
  const msg = encodeURIComponent('Halo Sahabat Mebel! Saya ingin konsultasi furnitur. Bisa bantu saya?');
  window.open(`https://wa.me/${WA_NUMBER}?text=${msg}`, '_blank');
});

// ── Social Proof Popup (NEW) ───────────────────────────────

const socialProofData = [
  { init: 'B', name: 'Budi S.',    msg: 'baru saja pesan Sofa Velvet Custom 🛋️',    time: '2 menit lalu' },
  { init: 'S', name: 'Sari D.',    msg: 'konsultasi Paket Interior Lengkap 🏠',       time: '5 menit lalu' },
  { init: 'R', name: 'Rizky P.',   msg: 'memesan Lemari Built-in Custom 🪵',           time: '8 menit lalu' },
  { init: 'A', name: 'Anita W.',   msg: 'baru saja pesan Meja Makan Jati 🍽️',        time: '12 menit lalu' },
  { init: 'D', name: 'Dinda K.',   msg: 'konsultasi Kamar Tidur Minimalis 🛏️',        time: '15 menit lalu' },
  { init: 'F', name: 'Fajar M.',   msg: 'pesan Meja Kerja Custom 💼',                  time: '18 menit lalu' },
  { init: 'Y', name: 'Yuni A.',    msg: 'order Rak Buku Built-in 📚',                  time: '22 menit lalu' },
];

let spIndex = 0;
const spPopup = document.getElementById('socialProofPopup');
const spClose = document.getElementById('spClose');

spClose?.addEventListener('click', () => spPopup?.classList.remove('show'));

function showSocialProof() {
  if (!spPopup) return;
  const d = socialProofData[spIndex % socialProofData.length];
  spIndex++;
  document.getElementById('spAvatar').textContent = d.init;
  document.getElementById('spName').textContent   = d.name;
  document.getElementById('spMsg').textContent    = d.msg;
  document.getElementById('spTime').textContent   = d.time;
  spPopup.classList.add('show');
  setTimeout(() => spPopup?.classList.remove('show'), 5000);
}

// Show first popup after 8s, then every 20s
setTimeout(() => {
  showSocialProof();
  setInterval(showSocialProof, 22000);
}, 8000);

// ── Scroll To Top (NEW) ────────────────────────────────────

const scrollTopBtn = document.getElementById('scrollTopBtn');

window.addEventListener('scroll', () => {
  scrollTopBtn?.classList.toggle('show', window.scrollY > 500);
}, { passive: true });

scrollTopBtn?.addEventListener('click', () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

// ── Scroll Animations (IntersectionObserver) ───────────────

const revealObserver = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity    = '1';
      entry.target.style.transform  = 'translateY(0) scale(1)';
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.10, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll(
  '.product-card, .testimonial-card, .fade-in, .trust-item, ' +
  '.room-card, .package-card, .milestone, .project-item, ' +
  '.partner-badge, .konsultasi-form-wrap'
).forEach(el => {
  el.style.opacity    = '0';
  el.style.transform  = 'translateY(24px) scale(0.98)';
  el.style.transition = 'opacity 0.55s ease, transform 0.55s ease';
  revealObserver.observe(el);
});

// ── Init ───────────────────────────────────────────────────
renderCart();
