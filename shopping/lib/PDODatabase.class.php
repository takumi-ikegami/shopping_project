<?php

namespace shopping\lib;

class PDODatabase
{
    private $dbh = null;
    private $db_host = '';
    private $db_user = '';
    private $db_pass = '';
    private $db_name = '';
    private $db_type = '';
    private $order = '';
    private $limit = '';
    private $offset = '';
    private $groupby = '';

    public function __construct($db_host, $db_user, $db_pass, $db_name, $db_type)
    {
        $this->dbh = $this->connectDB($db_host, $db_user, $db_pass, $db_name, $db_type);
        $this->db_host = $db_host;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_name = $db_name;
        //SQL関連
        $this->order = '';
        $this->limit = '';
        $this->offset = '';
        $this->groupby = '';
    }

    private function connectDB($db_host, $db_user, $db_pass, $db_name, $db_type)
    {
        //try-catch文、あらかじめ想定されたエラーが発生したときcatchの処理を行う
        //catch (捕まえる例外の型 例外の変数名)
        //PDO:「PHP Data Objects」の略で、PHPからデータベースへ簡単にアクセスするための拡張モジュール
        //バージョン違いのデータベース管理
        try {
            switch ($db_type) {
                    //mysqlの場合
                case 'mysql':
                    $dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name;
                    //PDOデータベースにアクセス第一接続情報、第二ユーザー名、第三パス
                    $dbh = new \PDO($dsn, $db_user, $db_pass);
                    //query:pdoメソッドsql文を実行
                    $dbh->query('SET NAMES utf8');
                    break;

                    //postgresqlの場合
                case 'pgsql':
                    $dsn = 'pgsql:dbname=' . $db_name . ' host=' . $db_host . ' port=5432';
                    $dbh = new \PDO($dsn, $db_user, $db_pass);
                    break;
            }
            //エラーが発生したときeにはエラーをオブジェクトとして格納
        } catch (\PDOException $e) {
            //getMessage():メッセージを取得するpdoメソッド
            var_dump($e->getMessage());
            exit();
        }
        return $dbh;
    }

    //prepared Statement : 穴埋め式SQL文エスケープ処理をする必要がない
    //prepareで準備?で定義
    //executeで実行、引数で配列を設定し、？に順番に代入
    //queryユーザーの入力情報がないときにはこちらの方が記述少ない
    //prepareではSQLインジェクションに対する対処が可能
    //bindValue
    public function setQuery($query = '', $arrVal = [])
    {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($arrVal);
    }

    public function select($table, $column = '', $where = '', $arrVal = [])
    {
        $sql = $this->getSql('select', $table, $where, $column);

        $this->sqlLogInfo($sql, $arrVal);

        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute($arrVal);

        if ($res === false) {
            $this->catchError($stmt->errorInfo());
        }

        $data = [];
        //fetch_assocと同じ処理のPDOver
        //採ってきた値を配列にしてresultに代入
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($data, $result);
        }
        return $data;
    }

    public function count($table, $where = '', $arrVal = [])
    {
        $sql = $this->getSql('count', $table, $where);

        $this->sqlLogInfo($sql, $arrVal);
        $stmt = $this->dbh->prepare($sql);

        $res = $stmt->execute($arrVal);
        if ($res === false) {
            $this->catchError($stmt->errorInfo());
        }

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return intval($result['NUM']);
    }

    public function setOrder($order = '')
    {
        if ($order !== '') {
            $this->order = ' ORDER BY ' . $order;
        }
    }

    public function setLimitOff($limit = '', $offset = '')
    {
        if ($limit !== "") {
            $this->limit = " LIMIT " . $limit;
        }
        if ($offset !== "") {
            $this->offset = " OFFSET " . $offset;
        }
    }

    public function setGroupBy($groupby)
    {
        if ($groupby !== "") {
            $this->groupby = ' GROUP BY ' . $groupby;
        }
    }

    private function getSql($type, $table, $where = '', $column = '')
    {
        switch ($type) {
            case 'select':
                $columnKey = ($column !== '') ? $column : "*";
                break;

            case 'count':
                $columnKey = 'COUNT(*) AS NUM';
                break;

            default:
                break;
        }

        if ($where !== '') {
            $whereSQL = ' WHERE ' . $where;
        } else {
            $whereSQL = '';
        }
        $other = $this->groupby . " " . $this->order . " " . $this->limit . " " . $this->offset;

        $sql = " SELECT " . $columnKey . " FROM " . $table . $whereSQL . $other;

        return $sql;
    }

    //tableに連想配列insDataのkey、valを入れる
    public function insert($table, $insData = [], $ins_mode = 'default')
    {
        $insDataKey = [];
        $insDataVal = [];
        $preCnt = [];

        $columns = '';
        $preSt = '';

        foreach ($insData as $col => $val) {
            $insDataKey[] = $col;
            $insDataVal[] = $val;
            $preCnt[] = '?';
        }

        //文字列化、sql文にするため
        $columns = implode(",", $insDataKey);
        $preSt = implode(",", $preCnt);

        switch ($ins_mode) {
            case 'regist':
                $sql = " INSERT INTO "
                    . $table
                    . " ("
                    . $columns
                    . ", regist_date"
                    . ") VALUES ("
                    . $preSt
                    . ",NOW()"
                    . " ) ";
                break;

            case 'buy':
                $sql = " INSERT INTO "
                    . $table
                    . " ("
                    . $columns
                    . ", buy_date"
                    . ") VALUES ("
                    . $preSt
                    . ",NOW()"
                    . " ) ";
                break;

            case 'sup':
                $sql = " INSERT INTO "
                    . $table
                    . " ("
                    . $columns
                    . ", sup_date"
                    . ") VALUES ("
                    . $preSt
                    . ",NOW()"
                    . " ) ";
                break;

            default:
                $sql = " INSERT INTO "
                    . $table
                    . " ("
                    . $columns
                    . ") VALUES ("
                    . $preSt
                    . " ) ";
                break;
        }

        $this->sqlLogInfo($sql, $insDataVal);

        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute($insDataVal);

        if ($res === false) {
            $this->catchError($stmt->errorInfo());
        }
        return $res;
    }

    public function update($table,  $insData = [], $where = '', $arrWhereVal = [], $upd_mode = 'default')
    {
        $arrPreSt = [];
        foreach ($insData as $col => $val) {
            $arrPreSt[] = $col . " = ? ";
        }
        $preSt = implode(',', $arrPreSt);


        switch ($upd_mode) {
            case 'update':
                $sql = " UPDATE "
                    . $table
                    . " SET "
                    . $preSt
                    . ", update_date = now() "
                    . " WHERE "
                    . $where;
                break;

            case 'buy':
                $sql = " UPDATE "
                    . $table
                    . " SET "
                    . $preSt
                    . " WHERE "
                    . $where;
                break;

            case 'delete':
                $sql = " UPDATE "
                    . $table
                    . " SET "
                    . $preSt
                    . ", delete_date = now() "
                    . " WHERE "
                    . $where;
                break;

            case 'repair':
                $sql = " UPDATE "
                    . $table
                    . " SET "
                    . $preSt
                    . ", delete_date = '' "
                    . " WHERE "
                    . $where;
                break;

            case 'spt_delete':
                $sql = " UPDATE "
                    . $table
                    . " SET "
                    . $preSt
                    . ", inc_date = now() "
                    . " WHERE "
                    . $where;
                break;

            case 'spt_repair':
                $sql = " UPDATE "
                    . $table
                    . " SET "
                    . $preSt
                    . ", inc_date = NULL "
                    . " WHERE "
                    . $where;
                break;

            case 'ord_delete':
                $sql = " UPDATE "
                    . $table
                    . " SET "
                    . $preSt
                    . ", ord_date = now() "
                    . " WHERE "
                    . $where;
                break;

            case 'ord_repair':
                $sql = " UPDATE "
                    . $table
                    . " SET "
                    . $preSt
                    . ", ord_date = NULL "
                    . " WHERE "
                    . $where;
                break;

            default:
                $sql = " UPDATE "
                    . $table
                    . " SET "
                    . $preSt
                    . " WHERE "
                    . $where;
                break;
        }

        //array_values、連想配列など渡された配列に添え字（数字）を付けて返す
        //array_merge、配列の結合、基本的に後ろに結合させていく、連想配列でも同様
        $updateData = array_merge(array_values($insData), $arrWhereVal);
        $this->sqlLogInfo($sql, $updateData);

        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute($updateData);

        if ($res === false) {
            $this->catchError($stmt->errorInfo());
        }
        return $res;
    }

    public function getLastId()
    {
        //lastInsertId():pdoメソッド最後にインサートされたidを取得する
        return $this->dbh->lastInsertId();
    }

    private function catchError($errArr = [])
    {
        $errMsg = (!empty($errArr[2])) ? $errArr[2] : "";
        die("SQLエラーが発生しました。" . $errMsg);
    }

    //logsにログを作成、実行されたSQL文を確認するために記述
    private function makeLogFile()
    {
        $logDir = dirname(__DIR__) . "/logs";
        //file_exists:ファイルがあるかの確認
        //ここではファイルがない場合に生成したいためfalseがtrueである必要である
        //つまりexistの否定が正である時を意味する
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777);
        }
        $logPath = $logDir . '/shopping.log';
        if (!file_exists($logPath)) {
            touch($logPath);
        }
        return $logPath;
    }

    private function sqlLogInfo($str, $arrVal = [])
    {
        $logPath = $this->makeLogFile();
        $logData = sprintf("[SQL_LOG:%s]: %s [%s]\n", date('Y-m-d H:i:s'), $str, implode(",", $arrVal));
        error_log($logData, 3, $logPath);
    }
}
