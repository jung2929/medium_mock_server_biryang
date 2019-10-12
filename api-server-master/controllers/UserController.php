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
         * API Name : USER ADD API
         * 마지막 수정 날짜 : 19.10.03
         */
        case "addUser":
			if(empty($req->type))
				$res->message = "<type> 공백입니다.".$res->message;
			if(empty($req->token))
				$res->message = "<token> 공백입니다.".$res->message;
			
			if(!empty($res->message)){		
                $res->message = "유저 가입 실패 : " . $res->message;
				$res->code = 201;
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}

			if(!empty(overlapCheckUser($req->email))) {
				$res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유저 가입 실패 : 중복";
			
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
            http_response_code(200);
            addUser($req->type, $req->token);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저가입 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 2
         * API Name : USER LOGIN API
         * 마지막 수정 날짜 : 19.10.03
         */
        case "loginUser":
			$token = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            http_response_code(200);
			
			if(empty($token)){
				$res->code = 201;
				$res->message = "<token> 공백입니다.";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				return;
			}
			
			$result = loginUser($token);
			
			if(empty($result)) {
				$res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "유효하지 않습니다.";
                addErrorLogs($errorLogs, $res, $req);
				echo json_encode($res, JSON_NUMERIC_CHECK);
                return; 
			}
			
			$jwt = getJWToken($result['userId'], JWT_SECRET_KEY);
			$res->result->userId =  $result['userId'];
            $res->result->jwt = $jwt;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "로그인 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
				
		
		/*
         * API No. 3
         * API Name : USER LIST API
         * 마지막 수정 날짜 : 19.10.03
         */			
		case "userList":
            http_response_code(200);
			
			if($req->pageNum < 0) {
				$res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "<pageNum> 0이하입니다." . $res->message;
			}
			if($req->pageCnt > 50){		
				$res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "<pageCnt> 50이상입니다." . $res->message;
			}
			if($res->code == 201){		
				$res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "조회 실패 : " . $res->message;
			
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
            $res->result = userList($req->pageNum, $req->pageCnt);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = $req->pageNum . "페이지 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 4
         * API Name : USER DETAIL API
         * 마지막 수정 날짜 : 19.10.04
         */			
		case "detailUser":
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
            $res->result = detailUser((int)$vars["userId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;				
		
		/*
         * API No. 5
         * API Name : USER UPDATA API
         * 마지막 수정 날짜 : 19.10.04
         */			
		case "updataUser":
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
            updataUser($vars["userId"], $req->name, $req->about, $req->image);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 정보 수정 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 6
         * API Name : USER FOLLOW ADD API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "followUser":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!isValidHeader($jwt, JWT_SECRET_KEY) || isValidHeader($jwt, JWT_SECRET_KEY) != $vars["userId"]) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
			if(!empty(overlapCheckFollowUser($vars["userId"], $req->followingId))) {
				$res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = $res->message . "중복된 데이터가 있습니다.";
			
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			};
		
            http_response_code(200);
            followUser($vars["userId"], $req->followingId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "팔로우 추가 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
		/*
         * API No. 7
         * API Name : USER FOLLOW DELETE API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "deleteFollowUser":
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
            deleteFollowUser($vars["userId"], $req->followingId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "팔로우 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
			
		/*
         * API No. 8
         * API Name : USER FOLLOWING LIST API
         * 마지막 수정 날짜 : 19.09.20
         */			
		case "followingUser":
            http_response_code(200);
            $res->result = followingUser($vars["userId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "팔로잉 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;	
			
		/*
         * API No. 9
         * API Name : USER FOLLOWER LIST API
         * 마지막 수정 날짜 : 19.09.20
         */			
		case "followerUser":
            http_response_code(200);
            $res->result = followerUser($vars["userId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "팔로워 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;	
			
		/*
         * API No. 10
         * API Name : USER BLOCK API
         * 마지막 수정 날짜 : 19.10.04
         */			
		case "blockUser":
			$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
			
			if (!isValidHeader($jwt, JWT_SECRET_KEY) || isValidHeader($jwt, JWT_SECRET_KEY) != $vars["userId"]) {
				$res->isSuccess = FALSE;
				$res->code = 301;
				$res->message = "유효하지 않은 토큰입니다";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			}
			
			if(!empty(overlapCheckBlockUser($vars["userId"], $req->blockId))) {
				$res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = $res->message . "중복된 데이터가 있습니다.";
			
				echo json_encode($res, JSON_NUMERIC_CHECK);
				addErrorLogs($errorLogs, $res, $req);
				return;
			};
            http_response_code(200);
            blockUser($vars["userId"], $req->blockId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 차단 추가";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;	
		
		/*
         * API No. 11
         * API Name : USER BLOCK DELETE API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "deleteBlockUser":
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
            deleteBlockUser($vars["userId"], $req->blockId);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 차단 삭제";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;		
		
		/*
         * API No. 12 
         * API Name : USER BLOCK LIST API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "blockUserList":
            http_response_code(200);
            $res->result = blockUserList($vars["userId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 차단 삭제";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;		
		
			
		/*
         * API No. 13
         * API Name : USER DELETE API
         * 마지막 수정 날짜 : 19.10.04
         */
        case "deleteUser":
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
            deleteUser($vars["userId"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
		
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}