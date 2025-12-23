const btn = document.querySelector('.menu-toggle');
const nav = document.getElementById('primary-nav');
function closeMenu(){ nav.classList.remove('open'); document.body.classList.remove('menu-open'); btn?.setAttribute('aria-expanded','false'); }
function toggleMenu(e){ e?.stopPropagation(); const open = nav.classList.toggle('open'); document.body.classList.toggle('menu-open', open); btn?.setAttribute('aria-expanded', open ? 'true' : 'false'); }
btn?.addEventListener('click', toggleMenu);
addEventListener('click', (e)=>{ if(!nav.classList.contains('open')) return; if(e.target.closest('#primary-nav')||e.target.closest('.menu-toggle')) return; closeMenu(); });
addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeMenu(); });
addEventListener('resize', ()=>{ if(innerWidth>900) closeMenu(); });  

