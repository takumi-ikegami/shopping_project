<?php

namespace shopping;

require_once dirname(__FILE__) . '/bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\Confirm;
use shopping\lib\login;
use shopping\lib\member;
use shopping\lib\Session;
use shopping\lib\PDODatabase;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$Confirm = new Confirm($db);
$login = new login($db);
$Mem = new Member($db);

$user_name = $_SESSION['login_user'];
$message = '';

if (isset($_SESSION["login_user"])) {
    $result_session = $login->session_serch($_SESSION['login_user'], '');
    if ($result_session[0]['user_name'] !== $_SESSION['login_user']) {
        header("Location:" . Bootstrap::ENTRY_URL . "login.php");
    }
} else {
    header("Location:" . Bootstrap::ENTRY_URL . "login.php");
}

//モード判定
//登録画面から来た場合
if (isset($_POST['confirm']) === true) {
    $mode = 'confirm';
}

//戻る場合
if (isset($_POST['back']) === true) {
    $mode = 'back';
}

//登録完了
if (isset($_POST['complete']) === true) {
    $mode = 'complete';
}

switch ($mode) {
    case 'confirm':
        $message = '';
        $dataArr = $_POST;

        unset($_POST['confirm']);

        if (isset($_POST['user_pass'])) {
            $result_member = $login->member_serch($_SESSION['login_user'], '');

            if (!password_verify($_POST['user_pass'], $result_member[0]['user_pass'])) {
                $message = "パスワードが違います";
            } else {
                $message = '';
            }
        }

        $template = ($message === '') ? 'delete_confirm.html.twig' : 'delete.html.twig';

        break;

    case 'back':
        $dataArr = $_POST;
        unset($_POST['back']);

        $template = 'delete.html.twig';

        break;

    case 'complete':
        $dataArr = $_POST;
        unset($_POST['complete']);

        $ins_arr = [];

        $ins_arr['delete_flg'] = '1';

        $res = $Confirm->updDeleteData($ins_arr, $user_name);

        if ($res === true) {
            $_SESSION['delete_flg'] = 'active';
            header('Location: ' . Bootstrap::ENTRY_URL . 'logout.php');
            exit();
        } else {
            //登録画面に戻る
            $template = 'delete.html.twig';
        }

        break;
}

if (isset($_SESSION['login_user'])) {
    $sessionData = $_SESSION;
}

$context['sessionData'] = $sessionData;
$context['dataArr'] = $dataArr;
$context['message'] = $message;
$template = $twig->loadTemplate($template);
$template->display($context);
