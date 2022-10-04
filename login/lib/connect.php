<?php

class connect
{

    const DB_HOST = "192.168.254.17";
    const DB_PORT = "1521";
    const DB_USERNAME = "ZNATU";
    const DB_PASSWORD = "ZNATU";
    const DB_SID = "orcl.world";

    private $conn;

    //=== コンストラクター
    public function __construct()
    {
        $conn = oci_connect(DB_USERNAME, DB_PASSWORD, DB_HOST . ":" . DB_PORT . "/" . DB_SID, 'AL32UTF8');

        try {

            if (!$conn) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
        } catch (Exception $e) {
            // 例外処理
            exit($e->getMessage());
        }

        // DBのエラーを表示するモードを設定
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    public function query($sql, $param = null)
    {
        $stid = oci_parse($this->conn, $sql);
        //====== 実行
        oci_execute($stid);
        return $stid;
    }
}
