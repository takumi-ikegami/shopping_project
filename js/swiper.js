const swiper = new Swiper(".swiper1", {
  effect: "coverflow",
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
  },
  speed: 1500,
  loop: true,
});

const swiper2 = new Swiper(".swiper2", {
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
  },
  slidesPerView: 3,
  speed: 1500,
  loop: true,
});
