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
			followUser.followingId = ? AND user.del = 'N'" ;

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
			followUser.followerId = ? AND user.del = 'N'" ;

		$st = $pdo->prepare($query);
		$st->execute([$userId]);

		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res;
	}
	