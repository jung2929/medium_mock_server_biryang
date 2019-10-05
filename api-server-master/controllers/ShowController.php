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
         * API Name : USER ADD API
         * 마지막 수정 날짜 : 19.09.21
         */
        case "showTimeLine":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!isValidHeader($jwt, JWT_SECRET_KEY) || isValidHeader($jwt, JWT_SECRET_KEY) != $vars["userId"]) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
			http_response_code(200);
            $res->result = showTimeLine($vars["userId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "타임라인 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 0
         * API Name : USER DELETE API
         * 마지막 수정 날짜 : 19.09.21
         */
        case "showComment":
			if(empty(checkPost($vars["userId"],$vars["postId"]))) {
				$res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = $res->message . "게시글 없음";
			
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
            http_response_code(200);
            $res->result = showComment($vars["postId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "게시글 코멘트 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
				
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}