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
         * API No. 1
         * API Name : TOPIC ADD API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "addTopic":
			http_response_code(200);
            addTopic($req->topics);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "토픽 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
			
		/*
         * API No. 2
         * API Name : USER FOLLOW ADD API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "followTopic":
		
            http_response_code(200);
            followTopic($req->topicId, $vars["userId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "팔로우 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		/*
         * API No. 3
         * API Name : USER FOLLOW DELETE API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "deleteFollowUser":
            http_response_code(200);
            deleteFollowUser($vars["userId"], $req->followingId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "팔로우 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}