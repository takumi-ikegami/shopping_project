<?php

namespace shopping;

//date_default_timezone_set:引数に文字列で指定した地域のタイムゾーンを設定し、実行結果としてboolean型の値を返す
//表示はdateで
date_default_timezone_set('Asia/Tokyo');

require_once dirname(__FILE__) . '/../vendor/autoload.php';

class Bootstrap
{
    const DB_HOST = 'mysql';
    const DB_NAME = 'shopping_db';
    const DB_USER = 'shopping_user';
    const DB_PASS = 'shopping_pass';
    const DB_TYPE = 'mysql';

    const APP_DIR = '/var/www/html/';
    const TEMPLATE_DIR = self::APP_DIR . 'templates/shopping/';
    const CACHE_DIR = false;
    const APP_URL = 'http://localhost:8080/';
    const ENTRY_URL = self::APP_URL . 'shopping/';

    public static function loadClass($class)
    {
        $path = str_replace('\\', '/', self::APP_DIR . $class . '.class.php');
        require_once $path;
    }
}
spl_autoload_register([
    'shopping\Bootstrap',
    'loadClass'
]);
