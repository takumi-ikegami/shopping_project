$(function () {
  var entry_url = $("#entry_url").val();

  $(".buyList").change(function () {
    var month = $(this).val();
    var year = $("#sup_year").val();
    var cor = $("#sup_cor").val();
    if (typeof year != "undefined" && typeof cor != "undefined") {
      location.href =
        entry_url +
        "buyListAd.php?month=" +
        month +
        "&year=" +
        year +
        "&cor=" +
        cor;
    } else if (typeof year != "undefined") {
      location.href =
        entry_url + "buyListAd.php?month=" + month + "&year=" + year;
    } else {
      location.href = entry_url + "buyListAd.php";
    }
  });
});
