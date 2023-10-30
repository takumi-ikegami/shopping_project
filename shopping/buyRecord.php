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

$page_id['page'] = 'member_page';

if (isset($_SESSION["login_user"])) {
    $result_session = $login->session_serch($_SESSION['login_user'], '');
    if ($result_session[0]['user_name'] !== $_SESSION['login_user']) {
        header("Location:" . Bootstrap::ENTRY_URL . "login.php");
    }
} else {
    header("Location:" . Bootstrap::ENTRY_URL . "login.php");
}

if (isset($_SESSION['login_user'])) {
    $sessionData = $_SESSION;
} else {
    $sessionData = '';
}

$buy_record = $cart->getBuyRecord($customer_no);

foreach ($buy_record as $buyKey => $buyVal) {
    $recordArr[$buyVal['buy_date']][] = $buyVal;
}

if (isset($recordArr)) {
    krsort($recordArr);
    $recordText = '';
} else {
    $recordArr =  '';
    $recordText = '購入履歴はありません';
}

$context = [];
$context['recordArr'] = $recordArr;
$context['recordText'] = $recordText;

$template = $twig->loadTemplate('buy_record.html.twig');
$template->display($context);
