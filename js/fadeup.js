class fadeup {
  constructor(target) {
    this.dom = target;
    this.dom.classList.add("is_active");
    this.target = document.querySelectorAll(".js_up.is_active");
  }
  animate() {
    gsap.to(this.target, {
      y: 0,
      opacity: 1,
      stagger: 0.08,
      delay: 0.2,
      duration: 0.7,
      ease: Power4.out,
    });
  }
}
