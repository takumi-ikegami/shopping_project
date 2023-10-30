/* ページトップ */
// document.addEventListener("DOMContentLoaded", function () {
//   new PageTop(".js_topBtn", 400);
// });

/* 監視処理(fadeUp) */
// document.addEventListener("DOMContentLoaded", function () {
//   const isp_fup = new Io(".js_up");
//   const el_fup = function (target) {
//     const fup = new fadeup(target);
//     fup.animate();
//   };
//   isp_fup.ob(el_fup);
// });

/* hummenu */
document.addEventListener("DOMContentLoaded", function () {
  const hum = new Nav(".js_hum", ".js_hum_listContainer");
  hum.hum_active();
});

/* ページトップ */
document.addEventListener("DOMContentLoaded", function () {
  new PageTop(".js_topBtn", 400);
});
