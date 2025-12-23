
  const btn = document.querySelector('.menu-toggle');
  const nav = document.getElementById('primary-nav');

  function closeMenu(){
    nav.classList.remove('open');
    document.body.classList.remove('menu-open');
    btn?.setAttribute('aria-expanded','false');
  }
  function toggleMenu(e){
    e?.stopPropagation();
    const open = nav.classList.toggle('open');
    document.body.classList.toggle('menu-open', open);
    btn?.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  btn?.addEventListener('click', toggleMenu);

  // close on outside click / ESC / desktop resize
  document.addEventListener('click', (e)=>{
    if(!nav.classList.contains('open')) return;
    if(e.target.closest('#primary-nav') || e.target.closest('.menu-toggle')) return;
    closeMenu();
  });
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeMenu(); });
  window.addEventListener('resize', ()=>{ if(innerWidth>900) closeMenu(); });


document.addEventListener('DOMContentLoaded', () => {
  const wrap = document.getElementById('readmeta');
  const totalEl = document.getElementById('readTotal');
  const inEl = document.getElementById('readIn');
  if (!wrap || !totalEl || !inEl) return;

  const ARTICLE = document.querySelector('.content');
  if (!ARTICLE) return;

  const clamp = (n,a,b)=>Math.min(Math.max(n,a),b);

  const words = (ARTICLE.innerText || '').trim().split(/\s+/).filter(Boolean).length;
  const totalMin = Math.max(1, Math.round(words / 220));
  totalEl.textContent = `~${totalMin}`;
  wrap.hidden = false;

  let maxP = 0;          // ✅ never decreases
  let lastShown = -1;

  const getScrollTop = () =>
    window.pageYOffset || document.documentElement.scrollTop || 0;

  function update(){
    const st = getScrollTop();
    const vh = window.innerHeight || 1;

    const rect = ARTICLE.getBoundingClientRect();
    const topAbs = rect.top + st;
    const height = ARTICLE.offsetHeight;

    const start = topAbs - vh * 0.15;
    const end   = topAbs + height - vh * 0.75;
    if (end <= start) return;

    const pNow = clamp((st - start) / (end - start), 0, 1);

    // ✅ lock progress so it never goes backwards
    if (pNow > maxP) maxP = pNow;

    // show 0 only before any progress
    if (maxP < 0.02 && lastShown === -1) {
      inEl.textContent = '0';
      return;
    }

    const minIn = clamp(Math.floor(maxP * totalMin), 1, totalMin);
    if (minIn !== lastShown) {
      inEl.textContent = String(minIn);
      lastShown = minIn;
    }
  }

  let ticking = false;
  const onScroll = () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => { ticking = false; update(); });
  };

  window.addEventListener('scroll', onScroll, { passive:true });
  window.addEventListener('resize', onScroll);

  setTimeout(update, 800);
});