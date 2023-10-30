class PageTop {
  constructor(target, no) {
    this.dom = document.querySelector(target);
    this.no = no;
    this.page_in(this.dom, this.no);
    this.top_move();
  }
  top_move() {
    this.dom.addEventListener("click", function () {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }
  page_in(dom, no) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > no) {
        if (dom.classList.contains("is_active") != true) {
          dom.classList.add("is_active");
          gsap.to(".js_topBtn", {
            y: 0,
            autoAlpha: 0.8,
            delay: 0.1,
            duration: 0.2,
            ease: Power4.out,
          });
        }
      } else {
        if (dom.classList.contains("is_active") == true) {
          dom.classList.remove("is_active");
          gsap.to(".js_topBtn", {
            y: 20,
            autoAlpha: 0,
            delay: 0.1,
            duration: 0.2,
            ease: Power4.out,
          });
        }
      }
    });
  }
}
