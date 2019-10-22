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
         * API Name : USER DELETE API
         * 마지막 수정 날짜 : 19.09.21
         */
        case "recentlyStory":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

			$pageNum = $_GET['pageNum'];
			$pageCnt = $_GET['pageCnt'];
			$sort = strtoupper($_GET['sort']);
						
			if(empty($pageNum))
				$res->message = "<pageNum> 공백입니다.".$res->message;
			if(empty($pageCnt))
				$res->message = "<pageCnt> 공백입니다.".$res->message;
			if(empty($sort)) {
				$sort = "DESC";
			}
			else if ( strtoupper($sort) != "ASC" AND strtoupper($sort) != "DESC"){
				$res->message = "<sort> asc, desc 가 아닙니다.".$res->message;
			}
			
			if(empty(!$res->message)){
				$res->isSuccess = FALSE;
				$res->code = 201;
				$res->message = "조회 실패 : ".$res->message;
				echo json_encode($res, JSON_NUMERIC_CHECK);
				return;
			}
			
			if (!$userId = isValidHeader($jwt, JWT_SECRET_KEY)) {
				$userId = 0;
			}
			
            http_response_code(200);
            $res->result = recentlyStory($userId, $pageNum, $pageCnt, $sort);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "최근 스토리 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		/*
         * API No. 0
         * API Name : USER DELETE API
         * 마지막 수정 날짜 : 19.09.21
         */
        case "recentlyList":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			$pageNum = $_GET['pageNum'];
			$pageCnt = $_GET['pageCnt'];
			$sort = strtoupper($_GET['sort']);
			
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
			if(empty($sort)) {
				$sort = "DESC";
			}
			else if ( strtoupper($sort) != "ASC" AND strtoupper($sort) != "DESC"){
				$res->message = "<sort> asc, desc 가 아닙니다.".$res->message;
			}
			
			if(empty(!$res->message)){
				$res->isSuccess = FALSE;
				$res->code = 201;
				$res->message = "조회 실패 : ".$res->message;
				echo json_encode($res, JSON_NUMERIC_CHECK);
				return;
			}
			
            http_response_code(200);
            $res->result = recentlyList($userId, $pageNum, $pageCnt, $sort);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "최근 스토리 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		case "popularStory":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			$pageNum = $_GET['pageNum'];
			$pageCnt = $_GET['pageCnt'];
			$sort = strtoupper($_GET['sort']);
			
			if(empty($pageNum))
				$pageNum = 1;
			if(empty($pageCnt))
				$pageCnt = 5;
			
			if (!$userId = isValidHeader($jwt, JWT_SECRET_KEY)) {
				$userId = 0;
			}

			if(empty($sort)) {
				$sort = "DESC";
			}
			else if ( strtoupper($sort) != "ASC" AND strtoupper($sort) != "DESC"){
				$res->message = "<sort> asc, desc 가 아닙니다.".$res->message;
			}
			
            http_response_code(200);
            $res->result = popularStory($userId, $pageNum, $pageCnt, $sort);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "인기 스토리 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		case "userStory":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			$pageNum = $_GET['pageNum'];
			$pageCnt = $_GET['pageCnt'];
			$sort = strtoupper($_GET['sort']);
			
			if(empty($pageNum))
				$pageNum = 1;
			if(empty($pageCnt))
				$pageCnt = 5;
			
			if (!$userId = isValidHeader($jwt, JWT_SECRET_KEY)) {
				$userId = 0;
			}
			
			if(empty($sort)) {
				$sort = "DESC";
			}
			else if ( strtoupper($sort) != "ASC" AND strtoupper($sort) != "DESC"){
				$res->message = "<sort> asc, desc 가 아닙니다.".$res->message;
			}
			
            http_response_code(200);
            $res->result = userStory($userId, $vars['userId'], $pageNum, $pageCnt, $sort);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;	
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}