<?php

//DB 정보
function pdoSqlConnect()
{
    try {
        $DB_HOST = "IP";
        $DB_NAME = "DB_NAME";
        $DB_USER = "USER";
        $DB_PW = "PASSWORD";
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PW);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}