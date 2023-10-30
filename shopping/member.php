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

$user_name = $_SESSION['login_user'];

if (isset($_SESSION["login_user"])) {
    $result_session = $login->session_serch($_SESSION['login_user'], '');
    if ($result_session[0]['user_name'] !== $_SESSION['login_user']) {
        header("Location:" . Bootstrap::ENTRY_URL . "login.php");
    }
} else {
    header("Location:" . Bootstrap::ENTRY_URL . "login.php");
}

$dataArr[] = $Mem->getMemberList($user_name)[0];

if (isset($dataArr[0]['user_pass'])) {
    $dataArr[0]['user_pass'] = '******';
} else {
    $dataArr[0]['user_pass'] = '';
}

$context = [];
$context['sessionData'] = $sessionData;
$context['dataArr'] = $dataArr[0];
$template = $twig->loadTemplate('member.html.twig');
$template->display($context);
