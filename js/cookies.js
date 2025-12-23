(function(){
const banner = document.getElementById('cookie-banner');
const btn = document.getElementById('acceptCookies');

function afterIdle(fn){ if('requestIdleCallback' in window){ requestIdleCallback(fn,{timeout:4000}); } else { setTimeout(fn,1500); } }

if(!localStorage.getItem('cookiesAccepted')){
    banner.style.display='block'; banner.style.opacity='0';
    setTimeout(()=>banner.style.transition='opacity .5s ease',50);
    setTimeout(()=>banner.style.opacity='1',100);
} else { afterIdle(loadAnalytics); }

btn?.addEventListener('click', ()=>{
    localStorage.setItem('cookiesAccepted','true');
    banner.style.opacity='0'; setTimeout(()=>banner.remove(),350);
    afterIdle(loadAnalytics);
});

function loadAnalytics(){
    // Google Analytics
    const ga1=document.createElement('script'); ga1.async=true;
    ga1.src='https://www.googletagmanager.com/gtag/js?id=G-RYJMZ5MVRY';
    document.head.appendChild(ga1);
    window.dataLayer=window.dataLayer||[];
    function gtag(){dataLayer.push(arguments);}
    gtag('js',new Date()); gtag('config','G-RYJMZ5MVRY');

    // Ahrefs
    const ahrefs=document.createElement('script'); ahrefs.async=true;
    ahrefs.src='https://analytics.ahrefs.com/analytics.js';
    ahrefs.setAttribute('data-key','zweMA87LDqQO2bvh5HVlIw');
    document.head.appendChild(ahrefs);

    // AdSense
    const ads=document.createElement('script'); ads.async=true;
    ads.src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7440179235836916';
    ads.crossOrigin='anonymous';
    document.head.appendChild(ads);
}
})();