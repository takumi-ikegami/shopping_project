<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\PDODatabase;
use shopping\lib\Session;
use shopping\lib\login;
use shopping\lib\member;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$Mem = new Member($db);
$login = new login($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->checkSession();

if (isset($_SESSION['login_user'])) {
    $sessionData = $_SESSION;
}

if (isset($_SESSION["login_user"])) {
    $result_session = $login->session_serch($_SESSION['login_user'], '');
    if ($result_session[0]['user_name'] !== $_SESSION['login_user']) {
        header("Location:" . Bootstrap::ENTRY_URL . "login.php");
    }
} else {
    header("Location:" . Bootstrap::ENTRY_URL . "login.php");
}

if (!isset($_SESSION['change_contents'])) {
    $_SESSION['change_contents'] = '';
}

if ($_GET['change_contents'] !== '') {
    if ($_SESSION['change_contents'] !== $_GET['change_contents']) {
        $_SESSION['change_contents'] = $_GET['change_contents'];
    }
}

$user_name = $_SESSION['login_user'];
$dataArr = [];

switch ($_SESSION['change_contents']) {
    case 'user';
        $dataArr = [
            'user_name' => ''
        ];
        break;

    case 'password';
        $dataArr = [
            'user_pass' => ''
        ];
        break;

    case 'name':
        $dataArr = [
            'family_name' => '',
            'first_name' => '',
            'family_name_kana' => '',
            'first_name_kana' => '',
        ];
        break;

    case 'address';
        $dataArr = [
            'zip1' => '',
            'zip2' => '',
            'address' => ''
        ];
        break;

    case 'mail';
        $dataArr = [
            'email' => ''
        ];
        break;

    case 'tel';
        $dataArr = [
            'tel1' => '',
            'tel2' => '',
            'tel3' => '',
        ];
        break;
}

$errArr = [];

$change = implode(',', array_keys($dataArr));
$oldDataArr = $Mem->getChangeList($user_name, $change)[0];

foreach ($dataArr as $key => $value) {
    $errArr[$key] = '';
}

$replace_Pass = '******';

$context = [];
$context['sessionData'] = $sessionData;
$context['oldDataArr'] = $oldDataArr;
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$context['replace_pass'] = $replace_Pass;
$template = $twig->loadTemplate('change.html.twig');
$template->display($context);
