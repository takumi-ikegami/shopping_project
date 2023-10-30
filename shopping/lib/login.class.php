<?php

namespace shopping\lib;

class login
{
    private $db = null;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function member_serch($post_name, $search_mode)
    {
        switch ($search_mode) {
            case 'admin':
                $table = ' admin ';
                $column = ' admin_pass ';
                $where = ' admin_name = ? ';
                $arrVal = [$post_name];
                break;

            default:
                $table = ' member ';
                $column = ' user_pass ';
                $where = ' user_name = ? AND delete_flg = 0 ';
                $arrVal = [$post_name];
                break;
        }


        return $this->db->select($table, $column, $where, $arrVal);
    }

    public function session_serch($post_name, $search_mode)
    {
        switch ($search_mode) {
            case 'admin':
                $table = ' session_admin ';
                $column = ' admin_name ';
                $where = ' admin_name = ? ';
                $arrVal = [$post_name];
                break;

            default:
                $table = ' session ';
                $column = ' user_name ';
                $where = ' user_name = ? ';
                $arrVal = [$post_name];

                break;
        }

        return $this->db->select($table, $column, $where, $arrVal);
    }

    //対象のテーブルのセッションkeyのみupdateしたい
    public function session_update_new($old_session, $session_key, $upd_name, $upd_mode)
    {
        switch ($upd_mode) {
            case 'admin':
                $table = ' session_admin ';
                $insData = ['session_key' => $session_key, 'admin_name' => $upd_name];
                $where = ' session_key = ? ';
                $arrWhereVal = [$old_session];
                break;

            default:
                $table = ' session ';
                $insData = ['session_key' => $session_key, 'user_name' => $upd_name];
                $where = ' session_key = ? ';
                $arrWhereVal = [$old_session];
                break;
        }

        return $this->db->update($table, $insData, $where, $arrWhereVal);
    }

    public function session_update($session_key, $upd_name, $upd_mode)
    {
        switch ($upd_mode) {
            case 'admin':
                $table = ' session_admin ';
                $insData = ['session_key' => $session_key];
                $where = ' admin_name = ? ';
                $arrWhereVal = [$upd_name];
                break;

            default:
                $table = ' session ';
                $insData = ['session_key' => $session_key];
                $where = ' user_name = ? ';
                $arrWhereVal = [$upd_name];
                break;
        }

        return $this->db->update($table, $insData, $where, $arrWhereVal);
    }
}
