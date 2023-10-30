$(function () {
  var entry_url = $("#entry_url").val();

  $("#cart_in").click(function () {
    var item_id = $("#item_id").val();
    var item_sum = $("#item_sum").val();
    location.href =
      entry_url + "cart.php?item_id=" + item_id + "&item_sum=" + item_sum;
  });

  $(".cart_reduce").change(function () {
    var cart_reduce = $(this).val();
    var cart_name = $(this).attr("name");
    var reduce_id = $(cart_name).val();
    location.href =
      entry_url +
      "cart.php?item_reduce=" +
      cart_reduce +
      "&reduce_id=" +
      reduce_id;
  });
});
