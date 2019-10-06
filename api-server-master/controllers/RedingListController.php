<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "API Server";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 0
         * API Name : ReadingList ADD API
         * 마지막 수정 날짜 : 19.10.07
         */
        case "addReadingList":
						
			http_response_code(200);
            addReadingList($req->userId, $req->storyId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "게시글 저장";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}