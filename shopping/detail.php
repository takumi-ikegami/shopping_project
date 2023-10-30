<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\PDODatabase;
use shopping\lib\Session;
use shopping\lib\Item;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$itm = new Item($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->checkSession();

if (isset($_SESSION['login_user'])) {
    $sessionData = $_SESSION;
} else {
    $sessionData = '';
}

$page_id['page'] = 'detail_page';

$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/', $_GET['item_id']) === 1) ? $_GET['item_id'] : '';

if ($item_id === '') {
    header('Location:' . Bootstrap::ENTRY_URL . 'list.php');
}

$cateArr = $itm->getCategoryList();

$itemData = $itm->getItemDetailData($item_id);

$context = [];
$context['sessionData'] = $sessionData;
$context['cateArr'] = $cateArr;
$context['page_id'] = $page_id;
$context['itemData'] = $itemData[0];
$template = $twig->loadTemplate('detail.html.twig');
$template->display($context);
