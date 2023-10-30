<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\Session;
use shopping\lib\PDODatabase;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->checkSession();

$page_id['page'] = 'regist_page';

if (isset($_SESSION["login_user"])) {
    header("Location:" . Bootstrap::ENTRY_URL . "index.php");
}

$dataArr = [
    'family_name' => '',
    'first_name' => '',
    'family_name_kana' => '',
    'first_name_kana' => '',
    'zip1' => '',
    'zip2' => '',
    'address' => '',
    'email' => '',
    'tel1' => '',
    'tel2' => '',
    'tel3' => '',
    'user_name' => '',
    'user_pass' => ''
];

$errArr = [];
foreach ($dataArr as $key => $value) {
    $errArr[$key] = '';
}

$context = [];

$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$context['page_id'] = $page_id;

$template = $twig->loadTemplate('regist.html.twig');
$template->display($context);
