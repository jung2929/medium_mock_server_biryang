<?php
	//CREATE USER
	function addReadingList($userId, $storyId){
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO readingList(userId, storyId) VALUES (?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$userId, $storyId]);

		$st = null;
		$pdo = null;
	}
	
	//UPDATE
	function updataRedingList($redingListId){
		$pdo = pdoSqlConnect();
		$query =
		"UPDATE
			redingList
		SET
			type = 'a',
			modifyAt = 
		WHERE
			id = ?";

		$st = $pdo->prepare($query);
		$st->execute([$contents, $location, $userId, $postId]);
		
		$st = null;
		$pdo = null;
	}
	
	//Delete
	function deleteRedingList($userId, $postId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE post SET del = 'Y' WHERE userId = ? AND postId = ?";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $postId]);

		$st = null;
		$pdo = null;
	}
		
	function likeList($postId){
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			user.userId,
			user.name,
            user.image
		FROM
			postLike
			inner join user on postLike.userId = user.userId
		WHERE
			postId = ? AND del = 'N'";
		
		$st = $pdo->prepare($query);
		$st->execute([$postId]);

		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res;
	}