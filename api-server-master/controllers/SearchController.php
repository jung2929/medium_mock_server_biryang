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
        case "searchStory":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			$search = $_GET['search'];
			$pageNum = $_GET['pageNum'];
			$pageCnt = $_GET['pageCnt'];
			
			if(empty($search))
				$res->message = "<search> 공백입니다.".$res->message;			
			if(empty($pageNum))
				$res->message = "<pageNum> 공백입니다.".$res->message;
			if(empty($pageCnt))
				$res->message = "<pageCnt> 공백입니다.".$res->message;
			
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
            $res->result = searchStory($userId, $search, $pageNum, $pageCnt);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 검색 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		/*
         * API No. 0
         * API Name : USER DELETE API
         * 마지막 수정 날짜 : 19.09.21
         */
        case "searchTopic":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			$search = $_GET['search'];
			$pageNum = $_GET['pageNum'];
			$pageCnt = $_GET['pageCnt'];
			
			if(empty($search))
				$res->message = "<search> 공백입니다.".$res->message;			
			if(empty($pageNum))
				$res->message = "<pageNum> 공백입니다.".$res->message;
			if(empty($pageCnt))
				$res->message = "<pageCnt> 공백입니다.".$res->message;
			
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
            $res->result = searchTopic($userId, $search, $pageNum, $pageCnt);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "토픽 검색 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 0
         * API Name : USER DELETE API
         * 마지막 수정 날짜 : 19.09.21
         */
        case "searchUser":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			$search = $_GET['search'];
			$pageNum = $_GET['pageNum'];
			$pageCnt = $_GET['pageCnt'];
			
			if(empty($search))
				$res->message = "<search> 공백입니다.".$res->message;			
			if(empty($pageNum))
				$res->message = "<pageNum> 공백입니다.".$res->message;
			if(empty($pageCnt))
				$res->message = "<pageCnt> 공백입니다.".$res->message;
			
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
            $res->result = searchUser($userId, $search, $pageNum, $pageCnt);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 검색 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		case "searchType":	
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			$type = $_GET['type'];
			$search = $_GET['search'];
			$pageNum = $_GET['pageNum'];
			$pageCnt = $_GET['pageCnt'];
			
			if(empty($type))
				$res->message = "<type> 공백입니다.".$res->message;		
			if(empty($search))
				$res->message = "<search> 공백입니다.".$res->message;			
			if(empty($pageNum))
				$res->message = "<pageNum> 공백입니다.".$res->message;
			if(empty($pageCnt))
				$res->message = "<pageCnt> 공백입니다.".$res->message;
			
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
			if($type == "story") {
				$res->result = searchStory($userId, $search, $pageNum, $pageCnt);
				$res->isSuccess = TRUE;
				$res->code = 100;
				$res->message = "스토리 검색 성공";
				echo json_encode($res, JSON_NUMERIC_CHECK);
			}
			else if($type == "topic") {
				$res->result = searchTopic($userId, $search, $pageNum, $pageCnt);
				$res->isSuccess = TRUE;
				$res->code = 100;
				$res->message = "토픽 검색 성공";
				echo json_encode($res, JSON_NUMERIC_CHECK);
			}
			else if($type == "user") {
				$res->result = searchUser($userId, $search, $pageNum, $pageCnt);
				$res->isSuccess = TRUE;
				$res->code = 100;
				$res->message = "유저 검색 성공";
				echo json_encode($res, JSON_NUMERIC_CHECK);
			}
			else {
				$res->isSuccess = FALSE;
				$res->code = 202;
				$res->message = "<type> (story, user, topic) 검색 실패";
				echo json_encode($res, JSON_NUMERIC_CHECK);
			}
            break;		
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}