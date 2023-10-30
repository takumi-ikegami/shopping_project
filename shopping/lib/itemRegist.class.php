<?php

namespace shopping\lib;

class itemRegist
{
    private $dataArr = [];
    private $errArr = [];
    private $db = null;

    //初期化
    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function insItemData($ins_arr)
    {
        $table = ' item ';
        return $this->db->insert($table, $ins_arr);
    }

    public function updItemData($ins_arr, $item_id)
    {
        $table = ' item ';
        $where = ' item_id = ? ';
        $arrWhereVal = [$item_id];
        return $this->db->update($table, $ins_arr, $where, $arrWhereVal);
    }

    public function itemDeleteData($item_id)
    {
        $table = ' item ';
        $where = ' item_id = ? ';
        $ins_arr['delete_flg'] = 1;
        $arrWhereVal = [$item_id];
        return $this->db->update($table, $ins_arr, $where, $arrWhereVal);
    }

    public function itemUpData($item_id)
    {
        $table = ' item ';
        $where = ' item_id = ? ';
        $ins_arr['delete_flg'] = 0;
        $arrWhereVal = [$item_id];
        return $this->db->update($table, $ins_arr, $where, $arrWhereVal);
    }

    public function errorCheck($dataArr)
    {
        $this->dataArr = $dataArr;
        $this->createErrorMessage();

        $this->itemNameCheck();
        $this->detailCheck();
        $this->priceCheck();
        $this->imageCheck();
        $this->ctg_idCheck();
        $this->stockCheck();

        return $this->errArr;
    }

    private function createErrorMessage()
    {
        foreach ($this->dataArr as $key => $val) {
            $this->errArr[$key] = '';
        }
    }

    private function itemNameCheck()
    {
        if (array_key_exists('item_name', $this->dataArr)) {
            if ($this->dataArr['item_name'] === '' || preg_match('/^.{0,100}$/', $this->dataArr['item_name']) === 0) {
                $this->errArr['item_name'] = '商品名を入力してください';
            }
        }
    }
    private function detailCheck()
    {
        if (array_key_exists('detail', $this->dataArr)) {
            if ($this->dataArr['detail'] === '' || preg_match('/^.{0,500}$/', $this->dataArr['item_name']) === 0) {
                $this->errArr['detail'] = '商品詳細を入力してください';
            }
        }
    }
    private function priceCheck()
    {
        if (array_key_exists('price', $this->dataArr)) {
            if ($this->dataArr['price'] === '' || preg_match('/^[0-9]{10}$/', $this->dataArr['item_name']) === 0) {
                $this->errArr['price'] = '価格を入力してください';
            }
        }
    }
    private function imageCheck()
    {
        if (array_key_exists('image', $this->dataArr)) {
            if ($this->dataArr['image'] === '') {
                $this->errArr['image'] = '正しく画像を登録してください';
            }
        }
    }
    private function ctg_idCheck()
    {
        if (array_key_exists('ctg_id', $this->dataArr)) {
            if ($this->dataArr['ctg_id'] === '' || preg_match('/^[0-9]{3}$/', $this->dataArr['item_name']) === 0) {
                $this->errArr['ctg_id'] = 'カテゴリーidを入力してください';
            }
        }
    }
    private function stockCheck()
    {
        if (array_key_exists('stock', $this->dataArr)) {
            if ($this->dataArr['stock'] === '' || preg_match('/^[0-9]{3}$/', $this->dataArr['item_name']) === 0) {
                $this->errArr['stock'] = '在庫を入力してください';
            }
        }
    }

    public function getErrorFlg()
    {
        $err_check = true;
        foreach ($this->errArr as $key => $value) {
            if ($value !== '') {
                $err_check = false;
            }
        }
        return $err_check;
    }
}
