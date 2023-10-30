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

$ses->adCheckSession();

$page_id['page'] = 'front_page';

if (isset($_SESSION['login_admin'])) {
    $sessionData = $_SESSION;
}

if (isset($_SESSION["login_admin"])) {
    $result_session = $login->session_serch($_SESSION['login_admin'], 'admin');
    if ($result_session[0]['admin_name'] !== $_SESSION['login_admin']) {
        header("Location:" . Bootstrap::ENTRY_URL . "login_admin.php");
    }
} else {
    header("Location:" . Bootstrap::ENTRY_URL . "login_admin.php");
}

$context = [];
$context['sessionData'] = $sessionData;
$context['page_id'] = $page_id;
$template = $twig->loadTemplate('admin.html.twig');
$template->display($context);
