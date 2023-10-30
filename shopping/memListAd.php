<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\PDODatabase;
use shopping\lib\Session;
use shopping\lib\login;
use shopping\lib\Admin;
use shopping\lib\csv;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$admin = new Admin($db);
$login = new login($db);
$csv = new Csv();

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->adCheckSession();

$page_id['page'] = 'memList_page';

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

$del_mem_id = (isset($_GET['del_mem_id']) === true && preg_match('/^\d+$/', $_GET['del_mem_id']) === 1) ? $_GET['del_mem_id'] : '';
$re_mem_id = (isset($_GET['re_mem_id']) === true && preg_match('/^\d+$/', $_GET['re_mem_id']) === 1) ? $_GET['del_mem_id'] : '';
$dl_mem = (isset($_GET['dl_mem']) === true) ? $_GET['dl_mem'] : '';

if (isset($_GET['search']) === true) {
    $dataArr = $admin->getMemberList($_GET['search']);
} else {
    $dataArr = $admin->getMemberList('');
}

if ($del_mem_id !== '') {
    $res = $admin->delMemberData($del_mem_id);
    header("Location:" . Bootstrap::ENTRY_URL . "memListAd.php");
}

if ($re_mem_id !== '') {
    $res = $admin->reMemberData($re_mem_id);
    header("Location:" . Bootstrap::ENTRY_URL . "memListAd.php");
}

if ($dl_mem !== '') {
    $name = 'member';
    $filepath = Bootstrap::APP_DIR . 'shopping/csv/member.csv';
    $header = ['お名前', 'お名前(カナ)', '郵便番号', '住所', 'メールアドレス', '電話番号', 'ユーザーネーム', '削除履歴'];
    $contents = [];
    $contArr = [];
    foreach ($dataArr as $index => $arr) {
        foreach ($arr as $head => $content) {
            if ($head === 'first_name' || $head === 'first_name_kana' || $head === 'zip2' || $head === 'tel3' || $head === 'tel2') {
                $contArr[count($contArr) - 1] = end($contArr) . $content;
            } elseif ($head === 'mem_id') {
                continue;
            } else {
                $contArr[] = $content;
            }
        }
        $contents[] = $contArr;
        $contArr = [];
    }
    $csv->csvDlMem($name, $header, $contents, $filepath);
    header("Location:" . Bootstrap::ENTRY_URL . "memListAd.php");
}

$context = [];
$context['sessionData'] = $sessionData;
$context['dataArr'] = $dataArr;
$context['page_id'] = $page_id;
$template = $twig->loadTemplate('memListAd.html.twig');
$template->display($context);
