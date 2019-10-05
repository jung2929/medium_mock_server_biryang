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
         * API Name : STORY ALL ADD API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "addAllStory":
			//$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			http_response_code(200);
			
            addAllStory($req->userId, $req->title,$req->subTitle, $req->topicId, $req->contents);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 2
         * API Name : STORY ADD API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "addStory":
			//$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			http_response_code(200);
			
            $res->storyId = addStory($req->userId, $req->title, $req->subTitle, $req->topicId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		/*
         * API No. 3
         * API Name : STORY TEXT ADD API
         * 마지막 수정 날짜 : 19.10.05
         */
        case "addText":
			//$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			http_response_code(200);
			
            addText($vars["storyId"],$req->text);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 텍스트 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 4
         * API Name : STORY TEXTLIST ADD API
         * 마지막 수정 날짜 : 19.10.05
         */
        case "addTextList":
			//$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			http_response_code(200);
			
            addTextList($vars["storyId"],$req->textList);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 리스트 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 5
         * API Name : STORY IMAGE ADD API
         * 마지막 수정 날짜 : 19.10.05
         */
        case "addImage":
			//$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			http_response_code(200);
			
            addImage($vars["storyId"],$req->image);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 이미지 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 6
         * API Name : STORY QUOTATION ADD API
         * 마지막 수정 날짜 : 19.10.05
         */
        case "addQuotation":
			//$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			http_response_code(200);
			
            addQuotation($vars["storyId"],$req->quotation);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 인용구 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 7
         * API Name : STORY DELIMITER ADD API
         * 마지막 수정 날짜 : 19.10.05
         */
        case "addDelimiter":
			//$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			http_response_code(200);
			
            addDelimiter($vars["storyId"],$req->delimiter);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 구분자 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		/*
         * API No. 7
         * API Name : STORY READ API
         * 마지막 수정 날짜 : 19.10.05
         */
        case "readStory":
			//$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			http_response_code(200);
			
            $res->storyId = readStory($vars["storyId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
					
		/*
         * API No. 8
         * API Name : STORY DELETE API
         * 마지막 수정 날짜 : 19.10.05
         */
        case "deleteStory":
			//$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			http_response_code(200);
			
            deleteStory($vars["storyId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "스토리 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
					
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}