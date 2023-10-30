$(function () {
  $("#address_search").click(function () {
    let zip1 = $("#zip1").val();
    let zip2 = $("#zip1").val();

    let entry_url = $("#entry_url").val();

    //match:あった場合値を返し、そうでないときnull
    if (zip1.match(/[0-9]{3}/) === null) {
      alert("正確な郵便番号を入力してください。");
      return false;
    } else {
      $.ajax({
        type: "get",
        url:
          entry_url +
          "/postcode_search.php?zip1=" +
          escape(zip1) +
          "&zip2=" +
          escape(zip2),
      })
        .done(function (data) {
          if (data == "no" || data == "") {
            alert("該当する郵便番号がありません");
          } else {
            $("#address").val(data);
          }
        })

        .fail(function () {
          alert("読み込みに失敗しました");
        });
    }
  });
});
