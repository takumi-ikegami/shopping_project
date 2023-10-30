<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\login;
use shopping\lib\PDODatabase;
use shopping\lib\Session;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
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

$dataArr = [];

$dataArr = [
    'user_pass' => ''
];

$errArr = [];

foreach ($dataArr as $key => $value) {
    $errArr[$key] = '';
}

$context = [];
$context['sessionData'] = $sessionData;
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$template = $twig->loadTemplate('delete.html.twig');
$template->display($context);
