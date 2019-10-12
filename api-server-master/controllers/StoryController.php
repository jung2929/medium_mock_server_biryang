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
         * API Name : STORY ADD API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "addStory":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!$userId = isValidHeader($jwt, JWT_SECRET_KEY)) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
			if(empty($req->title)) {
				$res->isSuccess = FALSE;
				$res->message = "작성 실패 : <title> 공백입니다.".$res->message;
				$res->code = 201;
				echo json_encode($res, JSON_NUMERIC_CHECK);
				return;
			}
			
			http_response_code(200);
            $storyId = addStory($userId, $req->title, $req->subTitle, $req->topicId);
			$sequence = 1;
			foreach($req->contents as $val){
				addContents($storyId, $sequence, $req->contents[$sequence-1]->types, $req->contents[$sequence-1]->contents);
				$sequence++;
			}		   
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;			
			
		/*
         * API No. 1
         * API Name : STORY ADD API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "addAllStory":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!$userId = isValidHeader($jwt, JWT_SECRET_KEY)) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
			if(empty($req->title)) {
				$res->isSuccess = FALSE;
				$res->message = "작성 실패 : <title> 공백입니다.".$res->message;
				$res->code = 201;
				echo json_encode($res, JSON_NUMERIC_CHECK);
				return;
			}
			
			http_response_code(200);
            $res->storyId = addStory($userId, $req->title, $req->subTitle, $req->topicId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		/*
         * API No. 3
         * API Name : STORY CONTENTS ADD API
         * 마지막 수정 날짜 : 19.10.05
         */
        case "addContents":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
			$res->isSuccess = FALSE;
			if(empty($req->sequence))	$res->message = "<sequence> 공백입니다.".$res->message;
			if(empty($req->type))		$res->message = "<type> 공백입니다.".$res->message;
			if(empty($req->contents))	$res->message = "<contents> 공백입니다.".$res->message;
			
			if(empty(!$res->message)){
				$res->code = 201;
				$res->message = "작성 실패 : ".$res->message;
				echo json_encode($res, JSON_NUMERIC_CHECK);
				return;
			}
			
			http_response_code(200);
			
            addContents($vars["storyId"], $req->sequence, $req->type, $req->contents);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 내용 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		
		/*
         * API No. 7
         * API Name : STORY READ API
         * 마지막 수정 날짜 : 19.10.05
         */
        case "readStory":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (($userId = isValidHeader($jwt, JWT_SECRET_KEY)) != false) {
				echo $userId;
				if(!empty(overlapRecentlyList($userId, $vars["storyId"]))){
					updateRecentlyList($userId, $vars["storyId"]);
					$res->message = "업데이트";
				}
				else {
					addRecentlyList($userId, $vars["storyId"]);
					$res->message = "추가";
				}
			}
			else {
				$res->message = "비회원";
			}
			
			http_response_code(200);
            $res->result = readStory($vars["storyId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = $res->message . "스토리 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
					
		/*
         * API No. 8
         * API Name : STORY DELETE API
         * 마지막 수정 날짜 : 19.10.05
         */
        case "deleteStory":
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
            deleteStory($userId, $vars["storyId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
					
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}