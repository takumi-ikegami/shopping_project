<?php

namespace shopping\lib;

class Item
{
    public $cateArr = [];
    public $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getCategoryList()
    {
        $table = ' category ';
        $col = ' ctg_id, category_name, category_img ';
        $res = $this->db->select($table, $col);
        return $res;
    }

    public function getItemList($ctg_id, $mode = '')
    {
        $table = ' item ';
        $col = ' item_id, item_name, detail, price, image, ctg_id, stock, delete_flg ';
        switch ($mode) {
            case 'Ad':
                $where = ($ctg_id !== '') ? ' ctg_id = ?' : 'delete_flg = ?';
                $arrVal = ($ctg_id !== '') ? [$ctg_id, 0] : [0];
                break;

            default:
                $where = ($ctg_id !== '') ? ' ctg_id = ? AND delete_flg = ?' : 'delete_flg = ?';
                $arrVal = ($ctg_id !== '') ? [$ctg_id, 0] : [0];
                break;
        }

        $res = $this->db->select($table, $col, $where, $arrVal);

        return ($res !== false && count($res) !== 0) ? $res : false;
    }

    public function searchItemList($search, $mode = '')
    {
        $table = ' item ';
        $col = ' item_id, item_name, price, image, ctg_id ';
        switch ($mode) {
            case 'Ad':
                $where = ' item_name LIKE ' . "'%" . $search . "%'";
                $arrVal = [];
                break;

            default:
                $where = ' delete_flg = ? AND item_name LIKE ' . "'%" . $search . "%'";
                $arrVal = [0];
                break;
        }

        $res = $this->db->select($table, $col, $where, $arrVal);

        return ($res !== false && count($res) !== 0) ? $res : false;
    }

    public function getItemDetailData($item_id, $mode = '')
    {
        $table = ' item ';
        $col = ' item_id, item_name, detail, price, image, ctg_id, stock, delete_flg ';
        switch ($mode) {
            case 'Ad':
                $where = ($item_id !== '') ? ' item_id = ? ' : '';
                $arrVal = ($item_id !== '') ? [$item_id] : [];
                break;

            default:
                $where = ($item_id !== '') ? ' item_id = ? AND delete_flg = ?' : 'delete_flg = ?';
                $arrVal = ($item_id !== '') ? [$item_id, 0] : [0];
                break;
        }


        $res = $this->db->select($table, $col, $where, $arrVal);
        return ($res !== false && count($res) !== 0) ? $res : false;
    }
}
