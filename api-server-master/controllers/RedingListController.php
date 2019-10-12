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
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!$userId = isValidHeader($jwt, JWT_SECRET_KEY)) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}

			if(!empty(overlapReadingList($userId, $req->storyId))){
				$res->readingListId = selectRedingList($userId, $req->storyId);
				$res->isSuccess = TRUE;
				$res->code = 100;
				$res->message = "스토리 중복";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				return;
			}
				
			http_response_code(200);
            addReadingList($userId, $req->storyId);
			$res->readingListId = selectRedingList($userId, $req->storyId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 저장";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		case "archiveReadingList":	
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
            updataRedingList($userId, $vars["readingListId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 보관";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

		case "deleteReadingList":	
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
            deleteReadingList($userId, $vars["readingListId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "리스트 목록 삭제";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;			
		
		case "readReadingList":	
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			$pageNum = $_GET['pageNum'];
			$pageCnt = $_GET['pageCnt'];
			$type = $_GET['type'];			

			if (!$userId = isValidHeader($jwt, JWT_SECRET_KEY)) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			if(empty($pageNum))
				$res->message = "<pageNum> 공백입니다.".$res->message;
			if(empty($pageCnt))
				$res->message = "<pageCnt> 공백입니다.".$res->message;
			if(empty($type))
				$res->message = "<type> 공백입니다.".$res->message;
			
			if(!empty($res->message)){
				$res->code = 201;
				$res->message = "조회 실패 : ".$res->message;
				echo json_encode($res, JSON_NUMERIC_CHECK);
				return;
			}
			
			http_response_code(200);
            $res->result = readReadingList($userId, $type, $pageNum, $pageCnt);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;		
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}