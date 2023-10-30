<?php

namespace shopping;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use shopping\Bootstrap;
use shopping\lib\Session;
use shopping\lib\login;
use shopping\lib\PDODatabase;

//データベースに接続する
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
//セッションを使うことを宣言
$ses = new Session($db);
$login = new login($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->adCheckSession();

$old_session = session_id();

$page_id['page'] = 'login_page';

//ログイン状態の場合ログイン後のページにリダイレクト
if (isset($_SESSION["login_admin"])) {
    session_regenerate_id(TRUE);
    //ここでセッションをupdate
    //keyを新しく
    $new_session = session_id();
    $result_session = $login->session_serch($_SESSION["login_admin"], 'admin');
    if ($result_session[0]['admin_name'] === $_SESSION["login_admin"]) {
        $login->session_update_new($old_session, $new_session, $_SESSION["login_admin"], 'admin');
    } else {
        $login->session_update($new_session, $_SESSION["login_admin"], 'admin');
    }
    header("Location:" . Bootstrap::ENTRY_URL . "admin.php");
    exit();
}

//postされて来なかったとき
if (count($_POST) === 0) {
    $message = "";
} elseif (empty($_POST["admin_name"]) || empty($_POST["admin_pass"])) {
    //postされて来た場合
    //ユーザー名またはパスワードが送信されて来なかった場合
    $message = "管理者名とパスワードを入力してください";
} else {
    $result_adMember = $login->member_serch($_POST['admin_name'], 'admin');
    //検索したユーザー名に対してパスワードが正しいかを検証
    if (isset($result_adMember[0]['admin_pass'])) {
        if (!password_verify($_POST['admin_pass'], $result_adMember[0]['admin_pass'])) {
            $message = "管理者名かパスワードが違います";
        } else {
            session_regenerate_id(TRUE); //セッションidを再発行
            $new_session = session_id();
            $_SESSION["login_admin"] = $_POST['admin_name']; //セッションにログイン情報を登録
            $result_session = $login->session_serch($_POST['admin_name'], 'admin');

            if ($result_session[0]['admin_name'] !== $_POST['admin_name']) {
                $login->session_update_new($old_session, $new_session, $_SESSION["login_admin"], 'admin');
            } else {
                $login->session_update($new_session, $_SESSION["login_admin"], 'admin');
            }
            header("Location:" . Bootstrap::ENTRY_URL . "admin.php"); //ログイン後のページにリダイレクト
            exit();
        }
    } else {
        $message = "管理者名かパスワードが違います";
    }
}

$message = htmlspecialchars($message);

$context = [];
$context['message'] = $message;
$context['page_id'] = $page_id;

$template = $twig->loadTemplate('login_admin.html.twig');
$template->display($context);
