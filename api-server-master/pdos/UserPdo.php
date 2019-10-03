<?php
	//CREATE USER
	function addUser($email, $name, $about, $image){
		$pdo = pdoSqlConnect();
		$query =
		"INSERT INTO
			user(email, name, about, image)
		VALUES
			(?, ?, ?, ?)";

		$st = $pdo->prepare($query);
		$st->execute([$email, $name, $about, $image]);

		$st = null;
		$pdo = null;
	}

	//CREATE OVERLAP CHECK USER  
	function overlapCheckUser($email){
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			*
		FROM
			user
		WHERE
			email = ? AND del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$email]);

		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res;
	}
	
	//LOGIN USER
	function loginUser($email){
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			*
		FROM
			user
		WHERE
			email = ? AND del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$email]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		
		return $res[0];
	}

	//READ USER
	function userList($pageNum, $pageCnt)
	{
		$pageNum = $pageNum *  $pageCnt;
		
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			   *
		FROM
			user
		LIMIT
			$pageNum, $pageCnt";
			
		$st = $pdo->prepare($query);
		$st->execute([$pageNum, $pageCnt]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		
		return $res;
	}
	
	//READ detail USER
	function detailUser($userId)
	{
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			userId,
            email,
            name,
			about,
            image
		FROM
			user
		WHERE
			userId = ?";

		$st = $pdo->prepare($query);
		$st->execute([$userId]);

		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res[0];
	}
	
	//UPDATE
	function updataUser($userId, $name, $about, $image){
		$pdo = pdoSqlConnect();
		$query = "SELECT * FROM user WHERE userId = ?";

		$st = $pdo->prepare($query);
		$st->execute([$userId]);	
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		
		if(empty($name))		$name = $res[0]['name'];
		if(empty($about))		$about = $res[0]['about'];
		if(empty($image))		$image = $res[0]['image'];
		
		$pdo = pdoSqlConnect();
		$query =
		"UPDATE
			user
		SET
			name = ?,
			about = ?,
			image = ?
		WHERE
			userId = ?";

		$st = $pdo->prepare($query);
		$st->execute([$name, $about, $image, $userId]);
		$st = null;
		$pdo = null;
	}
	
	//FOLLOW USER
	function followUser($followerId, $followingId){
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO followUser (followerId, followingId) VALUES (?, ?);";

		$st = $pdo->prepare($query);
		$st->execute([$followerId, $followingId]);

		$st = null;
		$pdo = null;
	}
	
	//delete FOLLOW USER
	function deleteFollowUser($followerId, $followingId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE followUser SET del = 'Y' WHERE followerId = ? AND followingId = ?;";

		$st = $pdo->prepare($query);
		$st->execute([$followerId, $followingId]);

		$st = null;
		$pdo = null;
	}
	
	//FOLLOW OVERLAP CHECK USER  
	function overlapCheckFollowUser($followerId, $followingId){
		$pdo = pdoSqlConnect();
		$query = "SELECT * FROM followUser WHERE followerId = ? AND followingId = ? AND del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$followerId, $followingId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		return $res;
	}

	
	//READ FOLLOWER USER
	function followerUser($userId){
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
	function followingUser($userId){
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
	function blockUser($userId, $blockId)
	{
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO blockUser(userId, blockId) VALUES (?, ?);";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $blockId]);

		$st = null;
		$pdo = null;
	}
	//delete BLOCK USER
	function deleteBlockUser($userId, $blockId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE blockUser SET del = 'Y' WHERE userId = ? AND blockId = ?;";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $blockId]);

		$st = null;
		$pdo = null;
	}
	//BLOCK OVERLAP CHECK USER  
	function overlapCheckBlockUser($userId, $blockId){
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

	
	//READ detail USER
	function infoUser($userId)
	{
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			userId,
            name,
            image,
            comment,
			(SELECT COUNT(CASE WHEN userId = ? AND del = 'N' THEN 1 END) FROM post) postCnt,
			(SELECT COUNT(CASE WHEN followingId = ? THEN  1 END ) FROM followUser) followerCnt,
			(SELECT COUNT(CASE WHEN followerId = ? THEN 1 END) FROM followUser) followingCnt
		FROM
			user
		WHERE
			userId = ?;";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $userId, $userId, $userId]);

		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res[0];
	}
	
	
	
	//DELETE USER
	function deleteUser($userId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE user SET del = 'Y' WHERE userId = ?";

		$st = $pdo->prepare($query);
		$st->execute([$userId]);

		$st = null;
		$pdo = null;

	}