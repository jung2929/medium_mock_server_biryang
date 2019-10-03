<?php
	//FOLLOW USER
	function addTopic($topics){
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO topic (topics) VALUES (?);";

		$st = $pdo->prepare($query);
		$st->execute([$topics]);

		$st = null;
		$pdo = null;
	}
	
	//FOLLOW USER
	function followTopic($topicId, $userId){
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO followTopic (topicId, userId) VALUES (?, ?);";

		$st = $pdo->prepare($query);
		$st->execute([$topicId, $userId]);

		$st = null;
		$pdo = null;
	}
	
	//delete FOLLOW USER
	function deleteFollowTopic($topicId, $userId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE followTopic SET del = 'Y' WHERE topicId = ? AND userId = ?;";

		$st = $pdo->prepare($query);
		$st->execute([$topicId, $userId]);

		$st = null;
		$pdo = null;
	}
	
	//FOLLOW OVERLAP CHECK USER  
	function overlapCheckFollowTopic($topicId, $userId){
		$pdo = pdoSqlConnect();
		$query = "SELECT * FROM followTopic WHERE topicId = ? AND userId = ? AND del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$topicId, $userId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		return $res;
	}

	
	//READ FOLLOWER USER
	function followerTopic($userId){
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			user.userId,
			user.name,
            user.image
		FROM
			followUser
			inner join user on followUser.followerId = user.userId
			
		WHERE
			followUser.followingId = ?  AND followUser.del = 'N' AND user.del = 'N'" ;

		$st = $pdo->prepare($query);
		$st->execute([$userId]);

		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res;
	}
	
	//READ FOLLOWING USER
	function followingTopic($userId){
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			user.userId,
			user.name,
            user.image
		FROM
			followUser
			inner join user on followUser.followingId = user.userId
		WHERE
			followUser.followerId = ? AND followUser.del = 'N' AND user.del = 'N'" ;

		$st = $pdo->prepare($query);
		$st->execute([$userId]);

		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res;
	}
		


	//BLOCK READ
	function blockTopic($userId, $blockId)
	{
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO blockUser(userId, blockId) VALUES (?, ?);";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $blockId]);

		$st = null;
		$pdo = null;
	}
	//delete BLOCK USER
	function deleteBlockTopic($userId, $blockId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE blockUser SET del = 'Y' WHERE userId = ? AND blockId = ?;";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $blockId]);

		$st = null;
		$pdo = null;
	}
	//BLOCK OVERLAP CHECK USER  
	function overlapCheckBlockTopic($userId, $blockId){
		$pdo = pdoSqlConnect();
		$query = "SELECT * FROM blockUser WHERE userId = ? AND blockId = ? AND del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $blockId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		return $res;
	}
