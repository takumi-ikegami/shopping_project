<?php

namespace shopping\lib;

class initMaster
{
    // public static function getDate()
    // {
    //     $yearArr = [];
    //     $monthArr = [];
    //     $dayArr = [];

    //     // date("Y-m-d H:i:s"); 2001-03-10 17:16:18 (MySQL の DATETIME フォーマット)
    //     $next_year = date('Y') + 1;

    //     //年を作成
    //     for ($i = 1900; $i < $next_year; $i++) {
    //         //0埋め4桁に満たないとき0001のように表示
    //         //第一引数のフォーマットにしたがって第二引数を変換
    //         $year = sprintf("%04d", $i);
    //         $yearArr[$year] = $year . '年';
    //     }

    //     //月を作成
    //     for ($i = 1; $i < 13; $i++) {
    //         $month = sprintf("%02d", $i);
    //         $monthArr[$month] = $month . '月';
    //     }

    //     //日を作成
    //     for ($i = 1; $i < 32; $i++) {
    //         $day = sprintf("%02d", $i);
    //         $dayArr[$day] = $day . '日';
    //     }

    //     //多次元配列を返す
    //     return [$yearArr, $monthArr, $dayArr];
    // }

    // public static function getSex()
    // {
    //     $sexArr = ['1' => '男性', '2' => '女性'];
    //     return $sexArr;
    // }

    // public static function getTrafficWay()
    // {
    //     $trafficArr = ['徒歩', '自転車', 'バス', '電車', '車・バイク'];
    //     return $trafficArr;
    // }
}
