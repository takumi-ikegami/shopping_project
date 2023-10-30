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

$page_id['page'] = 'list_page';

if (isset($_SESSION['login_user'])) {
    $sessionData = $_SESSION;
} else {
    $sessionData = '';
}

$ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';

$cateArr = $itm->getCategoryList();

if (isset($_GET['search']) === true) {
    $dataArr = $itm->searchItemList($_GET['search']);
} else {
    $dataArr = $itm->getItemList($ctg_id);
}

$context = [];
$context['sessionData'] = $sessionData;
$context['cateArr'] = $cateArr;
$context['dataArr'] = $dataArr;
$context['page_id'] = $page_id;
$template = $twig->loadTemplate('list.html.twig');
$template->display($context);
