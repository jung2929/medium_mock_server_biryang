<?php

//DB 정보
function pdoSqlConnect()
{
    try {
        $DB_HOST = "localhost";
        $DB_NAME = "MediumDB";
        $DB_USER = "marble";
        $DB_PW = "blue";
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PW);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}