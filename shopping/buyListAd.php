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

$page_id['page'] = 'buyList_page';

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

$del_buy_id = (isset($_GET['del_buy_id']) === true && preg_match('/^\d+$/', $_GET['del_buy_id']) === 1) ? $_GET['del_buy_id'] : '';
$re_buy_id = (isset($_GET['re_buy_id']) === true && preg_match('/^\d+$/', $_GET['re_buy_id']) === 1) ? $_GET['re_buy_id'] : '';
$dl_buy = (isset($_GET['dl_buy']) === true) ? $_GET['dl_buy'] : '';
$cor = (isset($_GET['cor']) === true) ? $_GET['cor'] : '';
$year = (isset($_GET['year']) === true) ? $_GET['year'] : '';
$month = (isset($_GET['month']) === true) ? $_GET['month'] : '';

if (isset($_GET['search']) === true) {
    $dataArr = $admin->getBuyList($_GET['search'], $cor, $year, $month);
} else {
    $dataArr = $admin->getBuyList('', $cor, $year, $month);
}

$yearArr = [];
for ($i = 2020; $i <= date('Y'); $i++) {
    $yearArr[] = $i;
}
//ここまでにデータアレイを作る
//csvダウンロードmonthを確認できていない


if ($del_buy_id !== '' || $re_buy_id !== '') {
    if ($del_buy_id === '' && $re_buy_id !== '') {
        $res = $admin->reBuyData($re_buy_id);
    } elseif ($del_buy_id !== '' && $re_buy_id === '') {
        $res = $admin->delBuyData($del_buy_id);
    }
    if ($cor !== '') {
        if ($year !== '' && $month !== '') {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php?cor=" . $cor . "&year=" . $year . "&month=" . $month);
        } elseif ($year !== '' && $month === '') {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php?cor=" . $cor . "&year=" . $year);
        } else {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php?cor=" . $cor);
        }
    } else {
        if ($year !== '' && $month !== '') {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php?year=" . $year . "&month=" . $month);
        } elseif ($year !== '' && $month === '') {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php?year=" . $year);
        } else {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php");
        }
    }
}

if ($dl_buy !== '') {
    $name = 'buy';
    $filepath = Bootstrap::APP_DIR . 'shopping/csv/buy.csv';
    $header = ['購入ID', '顧客ID', '合計数', '総額', '購入ID', '購入日付', '対応日付', '購入フラグ'];
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
    if ($cor !== '') {
        if ($year !== '' && $month !== '') {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php?cor=" . $cor . "&year=" . $year . "&month=" . $month);
        } elseif ($year !== '' && $month === '') {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php?cor=" . $cor . "&year=" . $year);
        } else {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php?cor=" . $cor);
        }
    } else {
        if ($year !== '' && $month !== '') {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php?year=" . $year . "&month=" . $month);
        } elseif ($year !== '' && $month === '') {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php?year=" . $year);
        } else {
            header("Location:" . Bootstrap::ENTRY_URL . "buyListAd.php");
        }
    }
}

$context = [];
$context['sessionData'] = $sessionData;
$context['dataArr'] = $dataArr;
$context['yearArr'] = $yearArr;
$context['cor'] = $cor;
$context['year'] = $year;
$context['month'] = $month;
$context['page_id'] = $page_id;
$template = $twig->loadTemplate('buyListAd.html.twig');
$template->display($context);
