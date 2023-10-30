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
$replace_Pass = '';

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
        //新規登録
        //データを受け継ぐ
        //unset:配列の要素を削除する、confirmは登録確認情報
        unset($_POST['confirm']);
        $dataArr = $_POST;
        //配列を削除して空の値を代入している
        //未定義でundefinedが表示されないようにするため
        if (isset($_POST['user_pass'])) {
            $replace_Pass = '******';

            $result_member = $login->member_serch($_SESSION['login_user'], '');

            if (!password_verify($_POST['old_user_pass'], $result_member[0]['user_pass'])) {
                $message = "ユーザー名かパスワードが違います";
            } else {
                $message = '';
            }
        } else {
            $replace_Pass = '';
        }

        //エラーメッセージの配列
        $errArr = $Confirm->errorCheck($dataArr);
        $err_check = $Confirm->getErrorFlg();

        $template = ($err_check === true && $message === '') ? 'change_confirm.html.twig' : 'change.html.twig';

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

        $template = 'change.html.twig';

        break;

    case 'complete':
        //登録完了
        $dataArr = $_POST;

        //変更したものがユーザーネームだった時ここでsessionのusernameを変える
        //updateの処理を書く
        unset($dataArr['complete']);
        $ins_arr = [];
        foreach ($dataArr as $key => $value) {
            if ($key === 'user_pass') {
                $password_h = password_hash($value, PASSWORD_DEFAULT);
                $value = $password_h;
            }
            $ins_arr[$key] = $value;
        }

        if (isset($ins_arr['old_user_pass'])) {
            unset($ins_arr['old_user_pass']);
        }

        $res = $Confirm->updRegistData($ins_arr, $user_name);

        if ($res === true) {
            //登録成功、完成ページへ
            //header ( $ヘッダ文字列 [, bool $replace = true [, int $http_response_code ]] )
            //”http/”から始まるヘッダを指定した場合は、HTTPステータスコードを示す
            //“Location:”ヘッダとURLを指定すると、指定したURLのブラウザを表示
            if (array_key_exists('user_name', $dataArr)) {
                $Confirm->updSessionData($ins_arr, $_SESSION["login_user"]);
                $_SESSION["login_user"] = $dataArr['user_name'];
                $user_name = $_SESSION['login_user'];
            }
            header('Location: ' . Bootstrap::ENTRY_URL . 'member.php');
            exit();
        } else {
            //登録画面に戻る
            $template = 'change.html.twig';

            foreach ($dataArr as $key => $value) {
                $errArr[$key] = '';
            }
        }
        break;
}

if (isset($dataArr['user_pass'])) {
    $change = 'user_pass';
} else {
    $change = implode(',', array_keys($dataArr));
}
$oldDataArr = $Mem->getChangeList($user_name, $change)[0];

if (isset($_SESSION['login_user'])) {
    $sessionData = $_SESSION;
}

$context['sessionData'] = $sessionData;
$context['replace_pass'] = $replace_Pass;
$context['oldDataArr'] = $oldDataArr;
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$context['message'] = $message;
$template = $twig->loadTemplate($template);
$template->display($context);
