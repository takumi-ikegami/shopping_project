<?php

namespace shopping\lib;

class Session
{
    public $session_key = '';
    public $db = NULL;

    public function __construct($db)
    {
        //session_start セッションの開始
        //session_id セッションIDを取得
        //ステートレス、情報を保持しない、処理が一往復で終わってしまう
        //ステートフル、情報を保持する通信方法
        //セッションIDに紐づけしてデータベースにデータを保存している
        //cookieデータをブラウザに保存する仕組み
        //セッション、ユーザーの一連の動作
        //セッションID、tmp,phpに保存されている
        session_start();
        $this->session_key = session_id();
        $this->db = $db;
    }

    //$_SESSION、session_startセッションが使用可能に
    //連想配列、任意のキーと値を設定可能
    //session_destoroyセッションを破棄する
    public function checkSession()
    {
        if (isset($_SESSION['login_user'])) {
            $login_user = $_SESSION['login_user'];
            $customer_no = $this->selectSession('login', $login_user);
        } else {
            $customer_no = $this->selectSession('', $this->session_key);
        }

        if ($customer_no !== false) {
            $_SESSION['customer_no'] = $customer_no;
        } else {
            $res = $this->insertSession();
            if ($res === true) {
                $_SESSION['customer_no'] = $this->db->getLastId();
            } else {
                $_SESSION['customer_no'] = '';
            }
        }
    }

    public function adCheckSession()
    {
        if (isset($_SESSION['admin_user'])) {
            $admin_user = $_SESSION['admin_user'];
            $admin_no = $this->adSelectSession('login', $admin_user);
        } else {
            $admin_no = $this->adSelectSession('', $this->session_key);
        }

        if ($admin_no !== false) {
            $_SESSION['admin_no'] = $admin_no;
        } else {
            $res = $this->adInsertSession();
            if ($res === true) {
                $_SESSION['admin_no'] = $this->db->getLastId();
            } else {
                $_SESSION['admin_no'] = '';
            }
        }
    }

    private function selectSession($select_mode, $arrWhere)
    {
        switch ($select_mode) {
            case 'login':
                $table = ' session ';
                $col = ' customer_no ';
                $where = ' user_name = ? ';
                $arrVal = [$arrWhere];

                //select
                $res = $this->db->select($table, $col, $where, $arrVal);
                return (count($res) !== 0) ? $res[0]['customer_no'] : false;
                break;

            default:
                $table = ' session ';
                $col = ' customer_no ';
                $where = ' session_key = ? ';
                $arrVal = [$arrWhere];

                //select
                $res = $this->db->select($table, $col, $where, $arrVal);
                return (count($res) !== 0) ? $res[0]['customer_no'] : false;
                break;
        }
    }

    private function adSelectSession($select_mode, $arrWhere)
    {
        switch ($select_mode) {
            case 'login':
                $table = ' session_admin ';
                $col = ' admin_no ';
                $where = ' user_name = ? ';
                $arrVal = [$arrWhere];

                //select
                $res = $this->db->select($table, $col, $where, $arrVal);
                return (count($res) !== 0) ? $res[0]['admin_no'] : false;
                break;

            default:
                $table = ' session_admin ';
                $col = ' admin_no ';
                $where = ' session_key = ? ';
                $arrVal = [$arrWhere];

                //select
                $res = $this->db->select($table, $col, $where, $arrVal);
                return (count($res) !== 0) ? $res[0]['admin_no'] : false;
                break;
        }
    }

    private function insertSession()
    {
        $table = ' session ';
        $insData = ['session_key ' => $this->session_key];
        //insert
        $res = $this->db->insert($table, $insData);
        return $res;
    }

    private function adInsertSession()
    {
        $table = ' session_admin ';
        $insData = ['session_key ' => $this->session_key];
        //insert
        $res = $this->db->insert($table, $insData);
        return $res;
    }
}
