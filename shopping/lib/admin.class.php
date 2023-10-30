<?php

namespace shopping\lib;

class Admin
{
    public $cateArr = [];
    public $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // public function getItemList($ctg_id)
    // {
    //     $table = ' item ';
    //     $col = ' item_id, item_name, price, image, ctg_id ';
    //     $where = ($ctg_id !== '') ? ' ctg_id = ? ' : '';
    //     $arrVal = ($ctg_id !== '') ? [$ctg_id] : [];

    //     $res = $this->db->select($table, $col, $where, $arrVal);

    //     return ($res !== false && count($res) !== 0) ? $res : false;
    // }

    // public function searchItemList($search)
    // {
    //     $table = ' item ';
    //     $col = ' item_id, item_name, price, image, ctg_id ';
    //     $where = ' item_name LIKE ' . "'%" . $search . "%'";
    //     $arrVal = [];

    //     $res = $this->db->select($table, $col, $where, $arrVal);

    //     return ($res !== false && count($res) !== 0) ? $res : false;
    // }

    public function getMemberList($user_name)
    {
        $table = ' member ';
        $col =
            'mem_id, family_name, first_name, family_name_kana, first_name_kana, zip1, zip2, address, email, tel1, tel2, tel3, user_name, delete_flg';
        $where = ($user_name !== '') ? ' user_name LIKE ' . "'%" . $user_name . "%'" : '';
        $arrVal = [];

        $res = $this->db->select($table, $col, $where, $arrVal);

        return ($res !== false) ? $res : false;
    }

    public function getIncList($user_name = '', $cor = '', $year = '', $month = '')
    {
        $table = ' support ';
        $col =
            'spt_id, support, spt_email, user_name, sup_date, inc_date, support_flg';
        $userSql = ($user_name !== '') ? ' user_name LIKE ' . "'%" . $user_name . "%'" . " AND " : '';
        if ($cor !== '') {
            if ($year !== '') {
                if ($month !== '') {
                    $sql = " support_flg = " . $cor . " AND sup_date " . ">= '" . $year . "-" . $month . "-01 00:00:00'" . " AND sup_date " . "<= '" . $year . "-" . $month . "-31 23:59:59'";
                    $where = ($userSql !== '') ? $userSql . $sql : $sql;
                } else {
                    $sql = " support_flg = " . $cor . " AND sup_date " . ">= '" . $year . "-01-01 00:00:00'" . " AND sup_date " . "< '" . $year + 1 . "-01-01 00:00:00'";
                    $where = ($userSql !== '') ? $userSql . $sql : $sql;
                }
            } else {
                $sql = " support_flg = " . $cor;
                $where = ($userSql !== '') ? $userSql . $sql : $sql;
            }
        } elseif ($cor === '') {
            if ($year !== '') {
                if ($month !== '') {
                    $sql = " sup_date " . ">= '" . $year . "-" . $month . "-01 00:00:00'" . " AND sup_date " . "<= '" . $year . "-" . $month . "-31 23:59:59'";
                    $where = ($userSql !== '') ? $userSql . $sql : $sql;
                } else {
                    $sql = " sup_date " . ">= '" . $year . "-01-01 00:00:00'" . " AND sup_date " . "< '" . $year + 1 . "-01-01 00:00:00'";
                    $where = ($userSql !== '') ? $userSql . $sql : $sql;
                }
            } else {
                $where = ($userSql !== '') ? $userSql : '';
            }
        }

        $arrVal = [];

        $res = $this->db->select($table, $col, $where, $arrVal);

        return ($res !== false) ? $res : false;
    }

    public function getBuyList($buy_id = '', $cor = '', $year = '', $month = '')
    {
        $table = ' buy ';
        $col =
            'buy_sort, customer_no, sum_num, sum_price, buy_id, buy_date, ord_date, ord_flg';
        $userSql = ($buy_id !== '') ? ' buy_id LIKE ' . "'%" . $buy_id . "%'" . " AND " : '';
        if ($cor !== '') {
            if ($year !== '') {
                if ($month !== '') {
                    $sql = " ord_flg = " . $cor . " AND buy_date " . ">= '" . $year . "-" . $month . "-01 00:00:00'" . " AND buy_date " . "<= '" . $year . "-" . $month . "-31 23:59:59'";
                    $where = ($userSql !== '') ? $userSql . $sql : $sql;
                } else {
                    $sql = " ord_flg = " . $cor . " AND buy_date " . ">= '" . $year . "-01-01 00:00:00'" . " AND buy_date " . "< '" . $year + 1 . "-01-01 00:00:00'";
                    $where = ($userSql !== '') ? $userSql . $sql : $sql;
                }
            } else {
                $sql = " ord_flg = " . $cor;
                $where = ($userSql !== '') ? $userSql . $sql : $sql;
            }
        } elseif ($cor === '') {
            if ($year !== '') {
                if ($month !== '') {
                    $sql = " buy_date " . ">= '" . $year . "-" . $month . "-01 00:00:00'" . " AND buy_date " . "<= '" . $year . "-" . $month . "-31 23:59:59'";
                    $where = ($userSql !== '') ? $userSql . $sql : $sql;
                } else {
                    $sql = " buy_date " . ">= '" . $year . "-01-01 00:00:00'" . " AND buy_date " . "< '" . $year + 1 . "-01-01 00:00:00'";
                    $where = ($userSql !== '') ? $userSql . $sql : $sql;
                }
            } else {
                $where = ($userSql !== '') ? $userSql : '';
            }
        }

        $arrVal = [];

        $res = $this->db->select($table, $col, $where, $arrVal);

        return ($res !== false) ? $res : false;
    }

    public function delMemberData($mem_id)
    {
        $table = ' cart ';
        $insData = ['delete_flg' => 1];
        $where = ' mem_id= ?';
        $arrWhereVal = [$mem_id];

        return $this->db->update($table, $insData, $where, $arrWhereVal, 'delete');
    }

    public function reMemberData($mem_id)
    {
        $table = ' cart ';
        $insData = ['delete_flg' => 0];
        $where = ' mem_id= ?';
        $arrWhereVal = [$mem_id];

        return $this->db->update($table, $insData, $where, $arrWhereVal, 'repair');
    }

    public function delIncData($spt_id)
    {
        $table = ' support ';
        $insData = ['support_flg' => 1];
        $where = ' spt_id= ?';
        $arrWhereVal = [$spt_id];

        return $this->db->update($table, $insData, $where, $arrWhereVal, 'spt_delete');
    }

    public function reIncData($spt_id)
    {
        $table = ' support ';
        $insData = ['support_flg' => 0];
        $where = ' spt_id= ?';
        $arrWhereVal = [$spt_id];

        return $this->db->update($table, $insData, $where, $arrWhereVal, 'spt_repair');
    }

    public function delBuyData($buy_sort)
    {
        $table = ' buy ';
        $insData = ['ord_flg' => 1];
        $where = ' buy_sort= ?';
        $arrWhereVal = [$buy_sort];

        return $this->db->update($table, $insData, $where, $arrWhereVal, 'ord_delete');
    }

    public function reBuyData($buy_sort)
    {
        $table = ' buy ';
        $insData = ['ord_flg' => 0];
        $where = ' buy_sort= ?';
        $arrWhereVal = [$buy_sort];

        return $this->db->update($table, $insData, $where, $arrWhereVal, 'ord_repair');
    }
}
