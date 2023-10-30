<?php

namespace shopping\lib;

class Cart
{
    private $db = null;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function insCartData($customer_no, $item_id, $item_sum)
    {
        //user_nameを基点にカートを登録する
        //session[user_name]があるときはmemnameで、そうでないときはcustomer_no
        $table = ' cart ';
        $insData = [
            'customer_no' => $customer_no,
            'item_id' => $item_id,
            'num' => $item_sum
        ];
        return $this->db->insert($table, $insData);
    }

    public function insBuyData($customer_no, $sumNum, $sumPrice, $buy_id, $ins_mode)
    {
        $table = ' buy ';
        $insData = [
            'customer_no' => $customer_no,
            'sum_num' => $sumNum,
            'sum_price' => $sumPrice,
            'buy_id' => $buy_id
        ];
        return $this->db->insert($table, $insData, $ins_mode);
    }

    public function getCartData($customer_no)
    {
        $table = ' cart c LEFT JOIN item i ON c.item_id = i.item_id ';
        $column = ' c.crt_id, c.num, i.item_id, i.item_name, i.price, i.image, i.stock';
        $where = ' c.customer_no = ? AND c.delete_flg= ?';
        $arrVal = [$customer_no, 0];

        return $this->db->select($table, $column, $where, $arrVal);
    }

    public function getCartId($customer_no)
    {
        $table = ' cart ';
        $column = ' crt_id, item_id, num ';
        $where = ' customer_no = ? AND delete_flg= ?';
        $arrVal = [$customer_no, 0];

        return $this->db->select($table, $column, $where, $arrVal);
    }

    public function getStock($item_id)
    {
        $table = ' item ';
        $column = ' stock, delete_flg ';
        $where = ' item_id = ? ';
        $arrVal = [$item_id];

        return $this->db->select($table, $column, $where, $arrVal);
    }

    public function reduceStock($crt_id, $item_num, $buy_num)
    {
        $table = ' cart c LEFT JOIN item i ON c.item_id = i.item_id ';
        $insData = [' i.stock ' => $item_num];
        $where = ' i.stock >= ' . $buy_num . ' AND crt_id= ?';
        $arrWhereVal = [$crt_id];

        return $this->db->update($table, $insData, $where, $arrWhereVal);
    }

    public function delCartData($crt_id)
    {
        $table = ' cart ';
        $insData = ['delete_flg' => 1];
        $where = ' crt_id= ?';
        $arrWhereVal = [$crt_id];

        return $this->db->update($table, $insData, $where, $arrWhereVal);
    }

    public function reduceCartData($cart_reduce, $crt_id)
    {
        $table = ' cart c LEFT JOIN item i ON c.item_id = i.item_id ';
        $insData = ['num' => $cart_reduce];
        $where = ' i.stock >= ' . $cart_reduce . ' AND crt_id= ? AND i.delete_flg= ?';
        $arrWhereVal = [$crt_id, 0];

        return $this->db->update($table, $insData, $where, $arrWhereVal);
    }

    public function buyCartData($crt_id, $buy_id)
    {
        $table = ' cart ';
        $insData = ['delete_flg' => 1, 'buy_flg' => 1, 'buy_id' => $buy_id];
        $where = ' crt_id= ? AND delete_flg= ?';
        $arrWhereVal = [$crt_id, 0];
        $upd_mode = 'buy';

        return $this->db->update($table, $insData, $where, $arrWhereVal, $upd_mode);
    }

    public function getItemAndSumPrice($customer_no)
    {
        $table = " cart c LEFT JOIN item i ON c.item_id = i.item_id";
        // ここで購入個数と価格を掛け算する
        $column = " SUM(i.price * c.num) AS totalPrice ";
        $where = ' c.customer_no = ? AND c.delete_flg = ?';
        $arrWhereVal = [$customer_no, 0];

        $res = $this->db->select($table, $column, $where, $arrWhereVal);
        $price = ($res !== false && count($res) !== 0) ? $res[0]['totalPrice'] : 0;

        $table = ' cart c ';
        $column = 'SUM(num) AS num ';
        $res = $this->db->select($table, $column, $where, $arrWhereVal);

        $num = ($res !== false && count($res) !== 0) ? $res[0]['num'] : 0;
        return [$num, $price];
    }

    public function getBuyRecord($customer_no)
    {
        $table = ' cart c LEFT JOIN buy b ON c.buy_id = b.buy_id LEFT JOIN item i ON c.item_id = i.item_id';
        $column = ' b.buy_date, c.buy_id, c.num, i.item_name, i.image ';
        $where = ' c.customer_no = ? AND c.delete_flg= ? AND c.buy_flg= ?';
        $arrVal = [$customer_no, 1, 1];

        return $this->db->select($table, $column, $where, $arrVal);
    }
}
