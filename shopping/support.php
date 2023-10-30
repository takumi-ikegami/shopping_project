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

if (isset($_SESSION['login_user'])) {
    $sessionData = $_SESSION;
}

$support_message = '';

if (isset($_SESSION['support_message'])) {
    if ($_SESSION['support_message'] === 'complete') {
        $support_message = 'お問い合わせを確認しました。';
    } elseif ($_SESSION['support_message'] === 'false') {
        $support_message = 'お問い合わせに失敗しました。';
    }
    $_SESSION['support_message'] = '';
}

$dataArr = [];

$page_id['page'] = 'support_page';

$dataArr = [
    'spt_email' => '',
    'support' => ''
];

$errArr = [];
foreach ($dataArr as $key => $value) {
    $errArr[$key] = '';
}

$context = [];

$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$context['page_id'] = $page_id;
if (isset($sessionData)) {
    $context['sessionData'] = $sessionData;
}
$context['support_message'] = $support_message;

$template = $twig->loadTemplate('support.html.twig');
$template->display($context);
