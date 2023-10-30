<?php

namespace shopping;

require_once dirname(__FILE__) . '/bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\Confirm;
use shopping\lib\Session;
use shopping\lib\PDODatabase;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$Confirm = new Confirm($db);

$page_id['page'] = 'support_page';


if (isset($_SESSION['login_user'])) {
    $login_user = $_SESSION['login_user'];
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
        //新規登録
        //データを受け継ぐ
        //unset:配列の要素を削除する、confirmは登録確認情報
        unset($_POST['confirm']);
        $dataArr = $_POST;

        //エラーメッセージの配列
        $errArr = $Confirm->errorCheck($dataArr);
        $err_check = $Confirm->getErrorFlg();

        $template = ($err_check === true) ? 'support_confirm.html.twig' : 'support.html.twig';

        //ループ処理の終了
        break;

    case 'back':
        //戻ってきたとき
        //postされたデータをもとに戻す
        $dataArr = $_POST;
        unset($dataArr['back']);

        //エラーを定義、Undefinedを回避
        foreach ($dataArr as $key => $value) {
            $errArr[$key] = '';
        }

        $template = 'support.html.twig';
        break;

    case 'complete':
        //登録完了
        $dataArr = $_POST;

        unset($dataArr['complete']);
        $ins_arr = [];
        foreach ($dataArr as $key => $value) {
            $ins_arr[$key] = $value;
        }

        $res = $Confirm->insSptData($ins_arr);

        if ($res === true) {
            //登録成功、完成ページへ
            //header ( $ヘッダ文字列 [, bool $replace = true [, int $http_response_code ]] )
            //”http/”から始まるヘッダを指定した場合は、HTTPステータスコードを示す
            //“Location:”ヘッダとURLを指定すると、指定したURLのブラウザを表示
            $_SESSION['support_message'] = 'complete';
            header('Location: ' . Bootstrap::ENTRY_URL . 'support.php');
            exit();
        } else {
            //登録画面に戻る
            $_SESSION['support_message'] = 'false';
            $template = 'support.html.twig';

            foreach ($dataArr as $key => $value) {
                $errArr[$key] = '';
            }
        }
        break;
}

$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$context['page_id'] = $page_id;
$template = $twig->loadTemplate($template);
$template->display($context);
