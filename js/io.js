//io一般設定
class Io {
  constructor(className) {
    this.elm = document.querySelectorAll(className);
    this.options = {
      root: null,
      rootMargin: "0px",
      threshold: 0.0,
    };
  }
  ob(anim) {
    const run = function (entries, observer) {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          anim(entry.target);
          observer.unobserve(entry.target);
        }
      });
    };
    const io = new IntersectionObserver(run, this.options);
    this.elm.forEach((target) => io.observe(target));
  }
}
