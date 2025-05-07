import { loadTheme, initThemeListeners } from "../../componentJS/theme-mode.js";
import { nav} from "../../componentJS/toolsFornavbar.js";

nav();
loadTheme();
initThemeListeners();

new Swiper('.myswiper', {
    loop: true,
    effect: 'fade',
    fadeEffect: {
      crossFade: true
    },   
    autoplay: {
      delay: 2500,
      disableOnInteraction: false,
    },
  });
new Swiper('.MYswiper', {
    loop: true, 
    slidesPerView: 3,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },    

    autoplay: {
        delay: 100000,
        disableOnInteraction: false,
      },
      breakpoints: {
        0:{
          slidesPerView: 1,
          spaceBetween: 10,
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },  
        },
        320: {
          slidesPerView:1,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 10,
        },
        992:{
          slidesPerView: 3,
          spaceBetween: 10,
        },
        1115: {
          slidesPerView: 3,
          spaceBetween: 10,
        },
      },

  });

/* Change Language */
import { translate } from "../../componentJS/translate.js";

const setLanguage = (lang) => {
  if (!translate[lang]) return;

  const elements = document.querySelectorAll(".lang-text[data-lang]");
  elements.forEach((el) => {
    const key = el.getAttribute("data-lang");
    el.textContent = translate[lang][key] || key;
  });

  document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
  localStorage.setItem("lang", lang);
};

document.querySelectorAll(".dropdown-item").forEach((item) => {
  item.addEventListener("click", () => {
    const selectedLang = item.getAttribute("data-lang");
    if (selectedLang) {
      setLanguage(selectedLang);
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const savedLang = localStorage.getItem("lang") || "en";
  setLanguage(savedLang);
})