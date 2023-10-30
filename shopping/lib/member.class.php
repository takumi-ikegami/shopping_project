<?php

namespace shopping\lib;

class Member
{
    public $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getMemberList($user_name)
    {
        $table = ' member ';
        $col =
            'mem_id, family_name, first_name, family_name_kana, first_name_kana, zip1, zip2, address, email, tel1, tel2, tel3, user_name, user_pass';
        $where = ($user_name !== '') ? ' user_name = ? ' : '';
        $arrVal = ($user_name !== '') ? [$user_name] : [];

        $res = $this->db->select($table, $col, $where, $arrVal);

        return ($res !== false) ? $res : false;
    }

    public function getChangeList($user_name, $change)
    {
        $table = ' member ';
        $col = $change;
        $where = ($user_name !== '') ? ' user_name = ? ' : '';
        $arrVal = ($user_name !== '') ? [$user_name] : [];

        $res = $this->db->select($table, $col, $where, $arrVal);

        return ($res !== false) ? $res : false;
    }
}
