<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\PDODatabase;
use shopping\lib\Session;
use shopping\lib\Cart;
use shopping\lib\Item;
use shopping\lib\login;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$cart = new Cart($db);
$itm = new Item($db);
$login = new login($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->checkSession();
$customer_no = $_SESSION['customer_no'];

if (isset($_SESSION["login_user"])) {
    $result_session = $login->session_serch($_SESSION['login_user'], '');
    if ($result_session[0]['user_name'] !== $_SESSION['login_user']) {
        header("Location:" . Bootstrap::ENTRY_URL . "login.php");
    }
} else {
    header("Location:" . Bootstrap::ENTRY_URL . "login.php");
}

$IdArr = $cart->getCartId($customer_no);

if (isset($_POST["buy"]) && isset($IdArr[0])) {
    $buy_id = uniqid();
    $ins_mode = 'buy';
    $sumNum = $_POST['sumNum'];
    $sumPrice = $_POST['sumPrice'];
    $cart->insBuyData($customer_no,  $sumNum, $sumPrice, $buy_id, $ins_mode);

    foreach ($IdArr as $key => $id) {
        $num = $cart->getStock($id['item_id'])[0]['stock'] - $id['num'];
        $cart->reduceStock($id['crt_id'], $num, $id['num']);
        $cart->buyCartData($id['crt_id'], $buy_id);
    }

    $_SESSION['buy_message'] = 'success';

    header("Location:" . Bootstrap::ENTRY_URL . "cart.php");
} else {
    $_SESSION['buy_message'] = 'false';
    header("Location:" . Bootstrap::ENTRY_URL . "cart.php");
}
