<?php

namespace shopping\lib;

class Csv
{
    public function csvDlMem($name, $header, $contents, $filepath)
    {
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=$name.csv");

        if (!$fp = fopen($filepath, 'w')) {
            echo 'ファイルを開けませんでした';
        } elseif (flock($fp, LOCK_EX) == false) {
            echo 'ファイルをロックできませんでした。';
        } else {
            fputcsv($fp, $header);

            foreach ($contents as $content) {
                fputcsv($fp, $content);
            }

            readfile($filepath);
            flock($fp, LOCK_UN);
            fclose($fp);
        }
        exit();
    }
}
