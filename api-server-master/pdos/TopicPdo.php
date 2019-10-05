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
	function readTopic(){
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			topicId,
			topics
		FROM
			topic
		WHERE
			del = 'N'" ;

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
	
	//READ FOLLOW TOPIC USER
	function readFollowTopic($userId){
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			*
		FROM
			topicUser
		WHERE
			userId = ? AND del = 'N'" ;

		$st = $pdo->prepare($query);
		$st->execute([$userId]);

		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res;
	}