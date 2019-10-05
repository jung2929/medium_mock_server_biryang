<?php
	//CREATE USER
	function showTimeLine($userId){
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			post.postId,
			userName.userId,
			userName.name,
			post.location,
			post.contents,
			(SELECT COUNT(CASE WHEN postId = post.postId THEN 1 END) FROM postLike) postLikeCnt,
			(SELECT COUNT(CASE WHEN postId = post.postId AND userId = userName.userId THEN 1 END) FROM postLike) postLike,
			post.createAt
		FROM
			post
			inner join user as userName on post.userId = userName.userId
			left join followUser on post.userId = followUser.followingId
		WHERE
			(post.userId = ? OR followUser.followerId = ? ) AND userName.del = 'N' AND post.del = 'N'
		ORDER BY post.createAt DESC";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $userId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		
		$st = null;
		$pdo = null;
		
		$cnt = 0;
		
		foreach($res as $postId) {
			$postId['postId'];
			$pdo = pdoSqlConnect();
			$query = 
			"SELECT
				image.image,
				image.createAt
			FROM
				image
				left join post on image.postId = post.postId
			WHERE
				image.postId = ?
			ORDER BY image.createAt";

			$st = $pdo->prepare($query);
			$st->execute([$postId['postId']]);
			
			$st->setFetchMode(PDO::FETCH_ASSOC);
			$res[$cnt]['image'] = $st->fetchAll();
			$cnt++;
		}
		
		$cnt = 0;
		
		foreach($res as $postId) {
			$postId['postId'];
			$pdo = pdoSqlConnect();
			$query = 
			"SELECT
				comment.commentId,
				comment.userId,
				#comment.postId,
				user.name,
				comment.commentGroup,
				comment.comment,
				comment.createAt
			FROM
				comment
				left join user on comment.userId = user.userId
			WHERE
				comment.postId = ?
			ORDER BY comment.createAt DESC LIMIT 2";

			$st = $pdo->prepare($query);
			$st->execute([$postId['postId']]);
			
			$st->setFetchMode(PDO::FETCH_ASSOC);
			$res[$cnt]['comment'] = $st->fetchAll();
			$cnt++;
		}
		
		return $res;
	}
	
	function showComment($postId){
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			post.postId,
			userName.name,
			post.contents,
			post.createAt
		FROM
			post
			inner join user as userName on post.userId = userName.userId
		WHERE
			post.postId = ? AND userName.del = 'N' AND post.del = 'N'
		ORDER BY post.createAt DESC";

		$st = $pdo->prepare($query);
		$st->execute([$postId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		
		$st = null;
		$pdo = null;
		
		$cnt = 0;
		
		foreach($res as $postId) {
			$postId['postId'];
			$pdo = pdoSqlConnect();
			$query = 
			"SELECT
				comment.commentId,
				comment.userId,
				#comment.postId,
				user.name,
				comment.commentGroup,
				comment.comment,
				comment.createAt
			FROM
				comment
				left join user on comment.userId = user.userId
			WHERE
				comment.postId = ?
			ORDER BY comment.createAt";

			$st = $pdo->prepare($query);
			$st->execute([$postId['postId']]);
			
			$st->setFetchMode(PDO::FETCH_ASSOC);
			$res[$cnt]['comment'] = $st->fetchAll();
			$cnt++;
		}
		
		return $res;
	}
	
	function timeLine2($userId, $postId){
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO postLike (userId, postId) VALUES ( ? , ?)";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $postId]);

		$st = null;
		$pdo = null;
	}