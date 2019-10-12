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
         * API Name : USER FOLLOW API
         * 마지막 수정 날짜 : 19.09.21
         */
        case "addComment":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!$userId = isValidHeader($jwt, JWT_SECRET_KEY)) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
            http_response_code(200);
            addComment($req->contentsId, $userId, $req->contents, $req->comment);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "코멘트 작성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		/*
         * API No. 0
         * API Name : USER FOLLOW API
         * 마지막 수정 날짜 : 19.09.21
         */
        case "deleteComment":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!$userId = isValidHeader($jwt, JWT_SECRET_KEY)) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
            http_response_code(200);
            deleteComment($vars["commentId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "코멘트 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		/*
         * API No. 0
         * API Name : USER DELETE API
         * 마지막 수정 날짜 : 19.09.21
         */
        case "updataComment":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!$userId = isValidHeader($jwt, JWT_SECRET_KEY)) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
		
            http_response_code(200);
            updataComment($vars["commentId"], $req->comment);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "코멘트 수정 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		 case "readComment":
			/*$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!isValidHeader($jwt, JWT_SECRET_KEY) || isValidHeader($jwt, JWT_SECRET_KEY) != $vars["userId"]) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}*/
		
            http_response_code(200);
            $res->result = readComment($vars["commentId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "코멘트 읽기 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		case "readStoryComment":
			/*$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!isValidHeader($jwt, JWT_SECRET_KEY) || isValidHeader($jwt, JWT_SECRET_KEY) != $vars["userId"]) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}*/
		
            http_response_code(200);
            $res->result = readStoryComment($vars["storyId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "코멘트 읽기 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}