class Nav {
  constructor(target, list) {
    this.dom = document.querySelector(target);
    this.list = document.querySelector(list);
  }

  hum_active() {
    let check = this.dom;
    let list = this.list;
    this.dom.addEventListener("click", function () {
      if (check.classList.contains("is_active") == false) {
        check.classList.add("is_active");
        list.classList.add("is_active");
      } else if (check.classList.contains("is_active")) {
        check.classList.remove("is_active");
        list.classList.remove("is_active");
      }
    });
  }
}
