<?php
	//SEARCH STORY
	function searchStory($userId, $search, $pageNum, $pageCnt){
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
			(SELECT readingList.id FROM readingList WHERE readingList.del = 'N' AND readingList.storyId = story.storyId AND readingList.userId = ?) as readingListId,
			(SELECT readingList.type FROM readingList WHERE readingList.del = 'N' AND readingList.storyId = story.storyId AND readingList.userId = ?) as readingType,
			story.createAt
		FROM
			story
			inner join user on story.userId = user.userId
			left join publicationUser on story.userId = publicationUser.userId
			left join publication on publicationUser.publicationId = publication.publicationId
			left join topic on story.topicId = topic.topicId
		WHERE
			user.del = 'N' AND story.del = 'N' AND story.title LIKE ?
		ORDER BY
			CASE  WHEN story.title LIKE  ? THEN  story.title  END DESC ,
			CASE  WHEN story.title LIKE  ? THEN  story.title END DESC
		LIMIT
			$pageNum, $pageCnt";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $userId, '%' . $search . '%', $search . '%' ,'%' . $search . '%']);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		
		foreach($res as $storyId) {
		
			$pdo = pdoSqlConnect();
			$query = 
			"SELECT
				contents.contents as SmallText
			FROM
				contents
			WHERE
				contents.storyId = ?  AND contents.type = 'SmallText'
			ORDER BY
				sequence
			LIMIT 1";

			$st = $pdo->prepare($query);
			$st->execute([ $storyId['storyId']]);
			
			$st->setFetchMode(PDO::FETCH_ASSOC);
			$contents = $st->fetchAll();
			$res[$cnt]['text'] = $contents[0]['SmallText'];
			
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
	
	//SEARCH TOPIC
	function searchTopic($userId, $search, $pageNum, $pageCnt){
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
			topic.del = 'N' AND topic.topics LIKE ?
		ORDER BY
			CASE  WHEN topic.topics LIKE  ? THEN  topic.topics  END DESC ,
			CASE  WHEN topic.topics LIKE  ? THEN  topic.topics END DESC
		LIMIT 
			$pageNum, $pageCnt" ;

		$st = $pdo->prepare($query);
		$st->execute([$userId, '%' . $search . '%', $search . '%' ,'%' . $search . '%']);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res  = $st->fetchAll();
				
		$st = null;
		$pdo = null;
		
		return $res;
	}
	
	//SEARCH USER
	function searchUser($userId, $search, $pageNum, $pageCnt){
		$pageNum = ($pageNum - 1) * $pageCnt;
		
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			user.userId,
            user.name,
			user.about,
            user.image,
			(SELECT COUNT(CASE WHEN userId = ? AND followingId = user.userId  AND del = 'N' THEN  1 END ) FROM followUser) isFollow
		FROM
			user
		WHERE
			user.name LIKE ?
		ORDER BY
			CASE  WHEN  user.name LIKE  ? THEN   user.name  END DESC ,
			CASE  WHEN  user.name LIKE  ? THEN   user.name END DESC
		LIMIT 
			$pageNum, $pageCnt" ;

		$st = $pdo->prepare($query);
		$st->execute([$userId, '%' . $search . '%', $search . '%' ,'%' . $search . '%']);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res  = $st->fetchAll();
				
		$st = null;
		$pdo = null;
		
		return $res;
	}