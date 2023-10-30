<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\PDODatabase;
use shopping\lib\Session;
use shopping\lib\Cart;
use shopping\lib\Item;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$cart = new Cart($db);
$itm = new Item($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->checkSession();
$customer_no = $_SESSION['customer_no'];

$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/', $_GET['item_id']) === 1) ? $_GET['item_id'] : '';
$item_sum = (isset($_GET['item_sum']) === true && preg_match('/^\d+$/', $_GET['item_sum']) === 1) ? $_GET['item_sum'] : '';
$crt_id = (isset($_GET['crt_id']) === true && preg_match('/^\d+$/', $_GET['crt_id']) === 1) ? $_GET['crt_id'] : '';
$reduce_id = (isset($_GET['reduce_id']) === true && preg_match('/^\d+$/', $_GET['reduce_id']) === 1) ? $_GET['reduce_id'] : '';
$cart_reduce = (isset($_GET['item_reduce']) === true && preg_match('/^\d+$/', $_GET['item_reduce']) === 1) ? $_GET['item_reduce'] : '';

if (isset($_SESSION['login_user'])) {
    $sessionData = $_SESSION;
} else {
    $sessionData = '';
}

$buy_message = '';

if (isset($_SESSION['buy_message'])) {
    if ($_SESSION['buy_message'] === 'success') {
        $buy_message = '購入に成功しました。';
    } elseif ($_SESSION['buy_message'] === 'false') {
        $buy_message = '購入に失敗しました。';
    }
    $_SESSION['buy_message'] = '';
}

$page_id['page'] = 'cart_page';

if ($item_id !== '' && $item_sum !== '') {
    $dataArr = $cart->getCartData($customer_no);
    if (in_array($item_id, array_column($dataArr, 'item_id'))) {
        $key = array_search($item_id, array_column($dataArr, 'item_id'));
        $reduce_id = $dataArr[$key]['crt_id'];
        $item_sum = $dataArr[$key]['num'] + $item_sum;
        $res = $cart->reduceCartData($item_sum, $reduce_id);
        if ($res === false) {
            $cart_message = "商品購入に失敗しました。";
            exit();
        }
    } else {
        $stock = $cart->getStock($item_id);
        if ($stock[0]['stock'] >= $item_sum && $stock[0]['delete_flg'] === "0") {
            $res = $cart->insCartData($customer_no, $item_id, $item_sum);
        } else {
            $res = false;
        }
        if ($res === false) {
            $_SESSION['buy_message'] = 'false';
        }
    }
    header("Location:" . Bootstrap::ENTRY_URL . "cart.php");
}

if ($crt_id !== '') {
    $res = $cart->delCartData($crt_id);
    header("Location:" . Bootstrap::ENTRY_URL . "cart.php");
}

if ($cart_reduce !== '' && $reduce_id !== '') {
    $res = $cart->reduceCartData($cart_reduce, $reduce_id);
    header("Location:" . Bootstrap::ENTRY_URL . "cart.php");
}

$dataArr = $cart->getCartData($customer_no);

foreach ($dataArr as $key => $value) {
    if ($value['num'] > $value['stock']) {
        $value['num'] = $value['stock'];
        $st = $cart->reduceCartData($value['num'], $value['crt_id']);
    }
}

//list:sumnum,sumpriceに持ってきた配列を代入
//ここで掛け算の処理
list($sumNum, $sumPrice) = $cart->getItemAndSumPrice($customer_no);

$cateArr = $itm->getCategoryList();

$context = [];
$context['sessionData'] = $sessionData;
$context['sumNum'] = $sumNum;
$context['sumPrice'] = $sumPrice;
$context['page_id'] = $page_id;
$context['cateArr'] = $cateArr;
$context['buy_message'] = $buy_message;
$context['dataArr'] = $dataArr;
$template = $twig->loadTemplate('cart.html.twig');
$template->display($context);
