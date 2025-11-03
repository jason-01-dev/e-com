// resources/js/app.js

import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Swiper (bundle inclut tous les modules nécessaires)
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

// Helper de debug
const log = (...args) => {
  if (window && window.console) console.log(...args);
};

document.addEventListener('DOMContentLoaded', () => {
  log('✅ app.js chargé — initialisation des Swiper...');

  // ---------- 1) Catalog carousels (.mySwiper) ----------
  const catalogSwipers = document.querySelectorAll('.mySwiper');
  if (catalogSwipers.length) {
    catalogSwipers.forEach((el, idx) => {
      try {
        new Swiper(el, {
          loop: true,
          autoplay: {
            delay: 3000,
            disableOnInteraction: false,
          },
          slidesPerView: 1,
          spaceBetween: 10,
          speed: 700,
          effect: 'slide',
          navigation: {
            nextEl: el.querySelector('.swiper-button-next') || '.swiper-button-next',
            prevEl: el.querySelector('.swiper-button-prev') || '.swiper-button-prev',
          },
          pagination: {
            el: el.querySelector('.swiper-pagination') || '.swiper-pagination',
            clickable: true,
          },
        });
        log(`🎠 Catalog Swiper #${idx} initialisé`);
      } catch (e) {
        console.warn('Erreur initialisation catalog Swiper:', e);
      }
    });
  } else {
    log('ℹ️ Aucun .mySwiper trouvé sur la page.');
  }

  // ---------- 2) Product page: main-slider + thumbnails ----------
  const mainSliderEl = document.getElementById('main-slider');
  const thumbsEl = document.getElementById('thumbnails-slider');

  if (mainSliderEl) {
    try {
      if (thumbsEl && thumbsEl.querySelectorAll('.swiper-slide').length > 0) {
        // Initialise le slider miniatures
        const thumbsSwiper = new Swiper(thumbsEl, {
          spaceBetween: 10,
          slidesPerView: Math.min(4, thumbsEl.querySelectorAll('.swiper-slide').length),
          freeMode: true,
          watchSlidesProgress: true,
          watchSlidesVisibility: true,
        });

        // Initialise le slider principal et connecte les miniatures
        const mainSwiper = new Swiper(mainSliderEl, {
          spaceBetween: 10,
          navigation: {
            nextEl: mainSliderEl.querySelector('.swiper-button-next') || '.swiper-button-next',
            prevEl: mainSliderEl.querySelector('.swiper-button-prev') || '.swiper-button-prev',
          },
          thumbs: {
            swiper: thumbsSwiper,
          },
        });

        log('🎯 Slider produit (main + thumbs) initialisé');
      } else {
        // Pas de miniatures : simple slider principal
        const mainSwiper = new Swiper(mainSliderEl, {
          loop: true,
          autoplay: {
            delay: 3500,
            disableOnInteraction: false,
          },
          slidesPerView: 1,
          spaceBetween: 10,
          speed: 700,
          navigation: {
            nextEl: mainSliderEl.querySelector('.swiper-button-next') || '.swiper-button-next',
            prevEl: mainSliderEl.querySelector('.swiper-button-prev') || '.swiper-button-prev',
          },
          pagination: {
            el: mainSliderEl.querySelector('.swiper-pagination') || '.swiper-pagination',
            clickable: true,
          },
        });

        log('🎯 Slider produit (main only) initialisé');
      }
    } catch (e) {
      console.warn('Erreur initialisation slider produit:', e);
    }
  } else {
    log('ℹ️ Aucun #main-slider trouvé sur la page.');
  }
});
