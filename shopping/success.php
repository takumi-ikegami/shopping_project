<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\Session;
use shopping\lib\PDODatabase;

//データベースに接続する
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
//セッションを使うことを宣言
$ses = new Session($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->checkSession();

if (!isset($_SESSION['login_user'])) {
    header('Location: login.php');
    exit();
}

$message = $_SESSION['login_user'] . 'さんようこそ';
$message = htmlspecialchars($message);

$context = [];
$context['message'] = $message;

$template = $twig->loadTemplate('success.html.twig');
$template->display($context);
