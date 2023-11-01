<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\PDODatabase;
use shopping\lib\Session;
use shopping\lib\login;
use shopping\lib\Admin;
use shopping\lib\Item;
use shopping\lib\itemRegist;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$admin = new Admin($db);
$login = new login($db);
$itm = new Item($db);
$iR = new itemRegist($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->adCheckSession();

$page_id['page'] = 'detailAd_page';
$errArr = '';

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

$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/', $_GET['item_id']) === 1) ? $_GET['item_id'] : '';
if (isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
}

$regtext = '';

if (isset($_SESSION['regtext'])) {
    if ($_SESSION['regtext'] !== '') {
        $regtext = $_SESSION['regtext'];
        $_SESSION['regtext'] = '';
    }
}

if (isset($_SESSION['item_id'])) {
    if ($_SESSION['item_id'] !== '') {
        $item_id = $_SESSION['item_id'];
        $_SESSION['item_id'] = '';
    }
}


if (isset($_POST['item_regist'])) {
    $dataArr = $_POST;

    $ins_arr = [];

    foreach ($dataArr as $key => $value) {
        if (isset($key['item_id']) && $key === 'item_id') {
            $item_id = $value;
        } elseif ($key === 'stock') {
            $now_stock = $value;
        } elseif ($key === 'add_stock') {
            if (isset($now_stock)) {
                $ins_arr['stock'] = $value + $now_stock;
            } else {
                $ins_arr['stock'] = $value;
            }
        } elseif ($key === 'image' || $key === 'imageUpd') {
            switch ($key) {
                case 'image':
                    if (!empty($_FILES['image']['name'])) {
                        $image = uniqid(mt_rand(), true);
                        $zip = '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
                        $image .= $zip;

                        if (preg_match('/.(jpg|jpeg|png|gif|bmp|svg|webp)$/', $image) === 1) {
                            $ins_arr['image'] = $image;
                            move_uploaded_file($_FILES['image']['tmp_name'], Bootstrap::APP_DIR . 'shopping/images/buy_items/' . $image);
                        } else {
                            $ins_arr['image'] = '';
                        }
                    } else {
                        $ins_arr['image'] = '';
                    }
                    break;

                case 'imageUpd':
                    if (!empty($_FILES['image']['name'])) {
                        $image = uniqid(mt_rand(), true);
                        $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);

                        //入力値がないとき戻したい
                        //変更では入力値がなくても通したい

                        if (preg_match('/.(jpg|jpeg|png|gif|bmp|svg|webp)$/', $image) === 1) {
                            $ins_arr['image'] = $image;
                            move_uploaded_file($_FILES['image']['tmp_name'], Bootstrap::APP_DIR . 'shopping/images/buy_items/' . $image);
                        } else {
                            $ins_arr['image'] = '';
                        }
                    } else {
                        $ins_arr['image'] = $value;
                    }
                    break;
            }
        } elseif ($key === 'item_regist') {
            continue;
        } else {
            $ins_arr[$key] = $value;
        }
    }


    $errArr = $iR->errorCheck($ins_arr);
    $err_check = $iR->getErrorFlg();


    if ($err_check) {
        if ($item_id === '') {
            $res = $iR->insItemData($ins_arr);
            if ($res) {
                $_SESSION['regtext'] = '登録に成功しました。';
                header("Location:" . Bootstrap::ENTRY_URL . "detailAd.php");
            } else {
                $_SESSION['regtext'] = '登録に失敗しました。';
                header("Location:" . Bootstrap::ENTRY_URL . "detailAd.php");
            }
        } elseif ($item_id !== '') {
            //updは追記
            $res = $iR->updItemData($ins_arr, $item_id);
            if ($res) {
                $_SESSION['regtext'] = '登録に成功しました。';
                $_SESSION['item_id'] = $item_id;
                header("Location:" . Bootstrap::ENTRY_URL . "detailAd.php");
            } else {
                $_SESSION['regtext'] = '登録に失敗しました。';
                $_SESSION['item_id'] = $item_id;
                header("Location:" . Bootstrap::ENTRY_URL . "detailAd.php");
            }
        }
    }
}

if (isset($_POST['item_delete']) && $item_id !== '') {
    if ($item_id !== '') {
        $res = $iR->itemDeleteData($item_id);
        if ($res) {
            $_SESSION['regtext'] = '削除に成功しました。';
            $_SESSION['item_id'] = $item_id;
            header("Location:" . Bootstrap::ENTRY_URL . "detailAd.php");
        } else {
            $_SESSION['regtext'] = '削除に失敗しました。';
            $_SESSION['item_id'] = $item_id;
            header("Location:" . Bootstrap::ENTRY_URL . "detailAd.php");
        }
    }
}

if (isset($_POST['item_Up']) && $item_id !== '') {
    if ($item_id !== '') {
        $res = $iR->itemUpData($item_id);
        if ($res) {
            $_SESSION['regtext'] = '復旧に成功しました。';
            $_SESSION['item_id'] = $item_id;
            header("Location:" . Bootstrap::ENTRY_URL . "detailAd.php");
        } else {
            $_SESSION['regtext'] = '復旧に失敗しました。';
            $_SESSION['item_id'] = $item_id;
            header("Location:" . Bootstrap::ENTRY_URL . "detailAd.php");
        }
    }
}

$cateArr = $itm->getCategoryList();

if ($item_id !== '') {
    $itemData = $itm->getItemDetailData($item_id, 'Ad');
} else {
    $itemData[0] = '';
}

$context = [];
$context['sessionData'] = $sessionData;
$context['errArr'] = $errArr;
$context['cateArr'] = $cateArr;
$context['page_id'] = $page_id;
$context['regtext'] = $regtext;
$context['item_id'] = $item_id;
$context['itemData'] = $itemData[0];
$template = $twig->loadTemplate('detailAd.html.twig');
$template->display($context);
