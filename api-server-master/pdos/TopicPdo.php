<?php
	//ADD TOPIC
	function addTopic($topics){
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO topic (topics) VALUES (?);";

		$st = $pdo->prepare($query);
		$st->execute([$topics]);

		$st = null;
		$pdo = null;
	}
	
	//DELETE TOPIC
	function deleteTopic($topicId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE topic SET del = 'Y' WHERE topicId = ? ";
		
		$st = $pdo->prepare($query);
		$st->execute([$topicId]);

		$st = null;
		$pdo = null;
	}
	
	//READ TOPIC
	function readTopic($userId, $pageNum, $pageCnt){
		$pageNum = ($pageNum - 1) * $pageCnt;
		
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			topic.topicId,
			topic.subject,
			topic.topics,
			(SELECT count(userId) FROM topicUser WHERE userId = ? AND topicId = topic.topicId AND del = 'N') as isTopic
		FROM
			topic
		WHERE
			topic.del = 'N'
		ORDER BY
			subject, topics
		LIMIT 
			$pageNum, $pageCnt" ;

		$st = $pdo->prepare($query);
		$st->execute([$userId]);

		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res;
	}
	
	//FOLLOW TOPIC
	function followTopic($topicId, $userId){
		$pdo = pdoSqlConnect();
		$query =
		"INSERT INTO
			topicUser (topicId, userId)
		SELECT ?, ?
		FROM DUAL
		WHERE NOT
		EXISTS(SELECT topicId, userId FROM topicUser WHERE topicId = ? AND userId =?)";

		$st = $pdo->prepare($query);
		$st->execute([$topicId, $userId, $topicId, $userId]);

		$st = null;
		$pdo = null;
	}
	
	//DELETE TOPIC USER
	function deleteFollowTopic($topicId, $userId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE topicUser SET del = 'Y' WHERE topicId = ? AND userId = ?;";

		$st = $pdo->prepare($query);
		$st->execute([$topicId, $userId]);

		$st = null;
		$pdo = null;
	}