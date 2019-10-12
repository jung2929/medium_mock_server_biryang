<?php
	//CREATE USER
	function overlapReadingList($userId, $storyId){
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			id
		FROM
			readingList
		WHERE
			userId = ? AND storyId = ? AND del = 'N'";
		
		$st = $pdo->prepare($query);
		$st->execute([$userId, $storyId]);

		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		
		$st = null;
		$pdo = null;
		
		return $res[0];
	}
	
	//CREATE USER
	function addReadingList($userId, $storyId){
		$pdo = pdoSqlConnect();
		$query =
		"INSERT INTO
			readingList(userId, storyId)
		VALUES
			(?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$userId, $storyId]);

		$st = null;
		$pdo = null;
	}
	
	//UPDATE
	function updataRedingList($userId, $readingListId){
		$createAt = date("Y-m-d h:i:s", time());
		
		$pdo = pdoSqlConnect();
		$query =
		"UPDATE
			readingList
		SET
			type = 'archive',
			createAt = ?
		WHERE
			userId = ? AND id = ?";

		$st = $pdo->prepare($query);
		$st->execute([$createAt, $userId , $readingListId]);
		
		$st = null;
		$pdo = null;
	}
	
	//Delete
	function deleteReadingList($userId, $readingListId){
		$pdo = pdoSqlConnect();
		$query =
		"UPDATE 
			readingList 
		SET 
			del = 'Y'
		WHERE
			userId = ? AND id = ?";

		$st = $pdo->prepare($query);
		$st->execute([$userId , $readingListId]);

		$st = null;
		$pdo = null;
	}
		
	//READ
	function readReadingList($userId, $type, $pageNum, $pageCnt){
		$pageNum = ($pageNum - 1) * $pageCnt;
		$cnt = 0;

		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			story.storyId,
			user.userId,
			user.name,
			story.title,
			story.subTitle,
			story.topicId,
			topic.topics,
			publication.publicationId,
			publication.publications,
			readingList.id as readingListId,
			readingList.type as readingType,
			story.createAt
		FROM
			readingList
			inner join story on readingList.storyId = story.storyId
			inner join user on story.userId = user.userId
			left join publicationUser on story.userId = publicationUser.userId
			left join publication on publicationUser.publicationId = publication.publicationId
			left join topic on story.topicId = topic.topicId
		WHERE
			readingList.userId = ? AND user.del = 'N' AND story.del = 'N' AND readingList.del = 'N' AND readingList.type = ?
		LIMIT
			$pageNum, $pageCnt";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $type]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		
		foreach($res as $storyId) {
		
			$pdo = pdoSqlConnect();
			$query = 
			"SELECT
				contents,contents as text
			FROM
				contents
			WHERE
				contents.storyId = ?  AND contents.type = 'text'
			ORDER BY
				sequence
			LIMIT 1";

			$st = $pdo->prepare($query);
			$st->execute([ $storyId['storyId']]);
			
			$st->setFetchMode(PDO::FETCH_ASSOC);
			$contents = $st->fetchAll();
			$res[$cnt]['text'] = $contents[0]['text'];
			
			$pdo = pdoSqlConnect();
			$query = 
			"SELECT
				contents,contents as image
			FROM
				contents
			WHERE
				contents.storyId = ?  AND contents.type = 'image'
			ORDER BY
				sequence
			LIMIT 1";

			$st = $pdo->prepare($query);
			$st->execute([ $storyId['storyId']]);
			
			$st->setFetchMode(PDO::FETCH_ASSOC);
			$contents = $st->fetchAll();
			$res[$cnt]['image'] = $contents[0]['image'];
			
			$cnt++;
		}
		
		$st = null;
		$pdo = null;
		
		return $res;
	}