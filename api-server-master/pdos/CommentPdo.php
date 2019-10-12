<?php
	//CREATE USER
	function addComment($sequenceId, $userId, $contents, $comment){
		if(empty($group)) $group = 0;
		
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO comment (contentsId, userId, contents, comment) VALUES (?, ?, ?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$sequenceId, $userId, $contents, $comment]);

		$st = null;
		$pdo = null;
	}
	
	function deleteComment($commentId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE comment SET del = 'Y' WHERE commentId = ?";

		$st = $pdo->prepare($query);
		$st->execute([$commentId]);

		$st = null;
		$pdo = null;
	}
	
	function updataComment($commentId, $comment){
		$pdo = pdoSqlConnect();
		$query =
		"UPDATE
			comment
		SET
			comment = ?
		WHERE
			commentId = ? AND del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$comment, $commentId]);

		$st = null;
		$pdo = null;
	}
	
	function readComment($commentId){
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			comment.commentId,
			comment.contentsId,
			user.userId,
			user.name,
			comment.contents,
			comment.comment,
			comment.createAt
		FROM
			comment
			inner join user on comment.userId = user.userid
		WHERE
			comment.commentId = ? AND comment.del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$commentId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		
		return $res[0];
	}
	
	function readStoryComment($storyId){
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			comment.commentId,
			comment.contentsId,
			user.userId,
			user.name,
			comment.contents,
			comment.comment,
			comment.createAt
		FROM
			comment
			inner join user on comment.userId = user.userid
			inner join contents on comment.contentsId = contents .contentsId
		WHERE
			contents.storyId = ? AND comment.del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		
		return $res;
	}
	
	function checkComment($userId, $postId, $commentId){
		$pdo = pdoSqlConnect();
		$query = "SELECT * FROM comment WHERE userId LIKE ? AND postId = ? AND commentId = ? AND del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $postId, $commentId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		return $res;
	}