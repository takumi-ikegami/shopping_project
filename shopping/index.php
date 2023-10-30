<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\Session;
use shopping\lib\PDODatabase;
use shopping\lib\Item;

//データベースに接続する
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
//セッションを使うことを宣言
$ses = new Session($db);
$itm = new Item($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->checkSession();
$sessionData = [];

$page_id['page'] = 'front_page';

if (isset($_SESSION['login_user'])) {
    $sessionData = $_SESSION;
}

$cateArr = $itm->getCategoryList();

$context = [];
$context['sessionData'] = $sessionData;
$context['page_id'] = $page_id;
$context['cateArr'] = $cateArr;
$template = $twig->loadTemplate('index.html.twig');
$template->display($context);
