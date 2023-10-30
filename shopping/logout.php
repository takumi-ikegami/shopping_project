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

if (array_key_exists('delete_flg', $_SESSION)) {
    $logout_mode = $_SESSION['delete_flg'];
} else {
    $logout_mode = '';
}


if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', 1, '/');
}

session_destroy();

switch ($logout_mode) {
    case 'active':
        header("Location:" . Bootstrap::ENTRY_URL . "delete_complete.php");
        break;

    default:
        header("Location:" . Bootstrap::ENTRY_URL . "index.php");
        break;
}

$context = [];

$template = $twig->loadTemplate($template);
$template->display($context);
