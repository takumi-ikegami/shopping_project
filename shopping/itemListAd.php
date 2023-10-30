<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\PDODatabase;
use shopping\lib\Session;
use shopping\lib\login;
use shopping\lib\Admin;
use shopping\lib\Item;
use shopping\lib\csv;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$admin = new Admin($db);
$login = new login($db);
$itm = new Item($db);
$csv = new Csv();

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->adCheckSession();

$page_id['page'] = 'itemList_page';

if (isset($_SESSION["login_admin"])) {
    $result_session = $login->session_serch($_SESSION['login_admin'], 'admin');
    if ($result_session[0]['admin_name'] !== $_SESSION['login_admin']) {
        header("Location:" . Bootstrap::ENTRY_URL . "login_admin.php");
    }
} else {
    header("Location:" . Bootstrap::ENTRY_URL . "login_admin.php");
}

if (isset($_SESSION['login_admin'])) {
    $sessionData = $_SESSION;
} else {
    $sessionData = '';
}

$ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';
$dl_mem = (isset($_GET['dl_mem']) === true) ? $_GET['dl_mem'] : '';

$cateArr = $itm->getCategoryList();

if (isset($_GET['search']) === true) {
    $dataArr = $itm->searchItemList($_GET['search'], 'Ad');
    $search = $_GET['search'];
} else {
    $dataArr = $itm->getItemList($ctg_id, 'Ad');
    $search = '';
}

if ($dl_mem !== '') {
    $name = 'item';
    $filepath = Bootstrap::APP_DIR . 'shopping/csv/itemList.csv';
    $header = ['商品id', '商品名', '商品詳細', '価格', '画像url', 'カテゴリーid', '在庫', '削除履歴'];
    $contents = [];
    $contArr = [];
    foreach ($dataArr as $index => $arr) {
        foreach ($arr as $head => $content) {
            $contArr[] = $content;
        }
        $contents[] = $contArr;
        $contArr = [];
    }
    $csv->csvDlMem($name, $header, $contents, $filepath);
    header("Location:" . Bootstrap::ENTRY_URL . "itemListAd.php");
}

$context = [];
$context['sessionData'] = $sessionData;
$context['cateArr'] = $cateArr;
$context['dataArr'] = $dataArr;
$context['ctg_id'] = $ctg_id;
$context['search'] = $search;
$context['page_id'] = $page_id;
$template = $twig->loadTemplate('itemListAd.html.twig');
$template->display($context);
