<?php

namespace shopping\lib;

class Confirm
{
    private $dataArr = [];
    private $errArr = [];
    private $db = null;

    //初期化
    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function insRegistData($regist_arr)
    {
        $table = ' member ';
        return $this->db->insert($table, $regist_arr, 'regist');
    }

    public function insSptData($regist_arr)
    {
        $table = ' support ';
        return $this->db->insert($table, $regist_arr, 'sup');
    }

    public function updRegistData($regist_arr, $user_name)
    {
        $table = ' member ';
        $where = ' user_name = ? ';
        $arrWhereVal = [$user_name];
        return $this->db->update($table, $regist_arr, $where, $arrWhereVal, 'update');
    }

    public function updDeleteData($regist_arr, $user_name)
    {
        $table = ' member ';
        $where = ' user_name = ? ';
        $arrWhereVal = [$user_name];
        return $this->db->update($table, $regist_arr, $where, $arrWhereVal, 'delete');
    }

    public function updSessionData($regist_arr, $user_name)
    {
        $table = ' session ';
        $where = ' user_name = ? ';
        $arrWhereVal = [$user_name];
        return $this->db->update($table, $regist_arr, $where, $arrWhereVal, 'default');
    }

    public function checkMail($check_email)
    {
        $table = ' member ';
        $email_arr = [$check_email];
        $where = ' email = ? ';

        $res = $this->db->count($table, $where, $email_arr);

        return ($res !== '') ? $res : '';
    }

    public function checkUser($check_user)
    {
        $table = ' member ';
        $user_arr = [$check_user];
        $where = ' user_name = ? ';

        $res = $this->db->count($table, $where, $user_arr);

        return ($res !== '') ? $res : '';
    }

    public function errorCheck($dataArr)
    {
        $this->dataArr = $dataArr;
        $this->createErrorMessage();

        $this->familyNameCheck();
        $this->firstNameCheck();
        $this->familyNameKanaCheck();
        $this->firstNameKanaCheck();
        $this->zipCheck();
        $this->addCheck();
        $this->telCheck();
        $this->mailCheck();
        $this->sptMailCheck();
        $this->supportCheck();
        $this->userCheck();
        $this->passCheck();

        return $this->errArr;
    }

    private function createErrorMessage()
    {
        foreach ($this->dataArr as $key => $val) {
            $this->errArr[$key] = '';
        }
    }

    private function familyNameCheck()
    {
        if (array_key_exists('family_name', $this->dataArr)) {
            if ($this->dataArr['family_name'] === '') {
                $this->errArr['family_name'] = 'お名前を（姓）を入力してください';
            }
        }
    }

    private function firstNameCheck()
    {
        if (array_key_exists('first_name', $this->dataArr)) {
            if ($this->dataArr['first_name'] === '') {
                $this->errArr['first_name'] = 'お名前を（名）を入力してください';
            }
        }
    }

    private function familyNameKanaCheck()
    {
        if (array_key_exists('family_name_kana', $this->dataArr)) {
            if ($this->dataArr['family_name_kana'] === '') {
                $this->errArr['family_name_kana'] = 'お名前を（セイ）を入力してください';
            }
        }
    }

    private function firstNameKanaCheck()
    {
        if (array_key_exists('first_name_kana', $this->dataArr)) {
            if ($this->dataArr['first_name_kana'] === '') {
                $this->errArr['first_name_kana'] = 'お名前を（メイ）を入力してください';
            }
        }
    }

    private function zipCheck()
    {
        if (array_key_exists('zip1', $this->dataArr) && array_key_exists('zip2', $this->dataArr)) {
            if (preg_match('/^[0-9]{3}$/', $this->dataArr['zip1']) === 0) {
                $this->errArr['zip1'] = '郵便番号の下は半角数字3桁で入力してください';
            }

            if (preg_match('/^[0-9]{4}$/', $this->dataArr['zip2']) === 0) {
                $this->errArr['zip2'] = '郵便番号の下は半角数字4桁で入力してください';
            }
        }
    }

    private function addCheck()
    {
        if (array_key_exists('address', $this->dataArr)) {
            if ($this->dataArr['address'] === '') {
                $this->errArr['address'] = '住所を入力してください';
            }
        }
    }

    private function supportCheck()
    {
        if (array_key_exists('support', $this->dataArr)) {
            if ($this->dataArr['support'] === '') {
                $this->errArr['support'] = 'お問い合わせ内容を入力してください';
            }
        }
    }

    private function mailCheck()
    {
        //*0回以上の繰り返し
        //+1回以上の繰り返し
        if (array_key_exists('email', $this->dataArr)) {
            $res = $this->checkMail($this->dataArr['email']);
            if (preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+[a-zA-Z0-9\._-]+$/', $this->dataArr['email']) === 0 || $res >= 1) {
                $this->errArr['email'] = 'メールアドレスの形式が間違っているか、または登録済みのメールアドレスです';
            }
        }
    }

    private function sptMailCheck()
    {
        //*0回以上の繰り返し
        //+1回以上の繰り返し
        if (array_key_exists('spt_email', $this->dataArr)) {
            if (preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+[a-zA-Z0-9\._-]+$/', $this->dataArr['spt_email']) === 0) {
                $this->errArr['spt_email'] = 'メールアドレスの形式が間違っています';
            }
        }
    }

    private function userCheck()
    {
        //*0回以上の繰り返し
        //+1回以上の繰り返し
        if (array_key_exists('user_name', $this->dataArr)) {
            $res = $this->checkUser($this->dataArr['user_name']);
            if (preg_match('/^[0-9]+$/', $this->dataArr['user_name']) === 0 || $res >= 1) {
                $this->errArr['user_name'] = 'ユーザーネームの形式が間違っているか、または登録済みのユーザーネームです';
            }
        }
    }
    private function passCheck()
    {
        //*0回以上の繰り返し
        //+1回以上の繰り返し
        if (array_key_exists('user_pass', $this->dataArr)) {
            if (preg_match('/^[0-9]+$/', $this->dataArr['user_pass']) === 0) {
                $this->errArr['user_pass'] = 'パスワードを正しい形式で入力して下さい';
            }
        }
    }

    private function telCheck()
    {
        if (array_key_exists('tel1', $this->dataArr) && array_key_exists('tel2', $this->dataArr) && array_key_exists('tel3', $this->dataArr)) {
            if (
                preg_match('/^\d{1,6}$/', $this->dataArr['tel1']) === 0 ||
                preg_match('/^\d{1,6}$/', $this->dataArr['tel2']) === 0 ||
                preg_match('/^\d{1,6}$/', $this->dataArr['tel3']) === 0 ||
                strlen($this->dataArr['tel1'] . $this->dataArr['tel2'] . $this->dataArr['tel3']) >= 12
            ) {
                $this->errArr['tel1'] = '電話番号は、半角数字で11桁以内で入力してください';
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
