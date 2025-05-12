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
  const logout = document.querySelector("#logout")
  logout.addEventListener("click", function () {
    localStorage.removeItem("lang");
    localStorage.removeItem("theme");
    window.location.href = 'http://localhost/Soft/auth/signup.html';
  });
