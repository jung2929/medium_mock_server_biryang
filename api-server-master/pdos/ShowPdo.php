<?php
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
	
	
	//READ STORY
	function recentlyStory($userId, $pageNum, $pageCnt){
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
			user.del = 'N' AND story.del = 'N'
		ORDER BY
			story.createAt DESC
		LIMIT
			$pageNum, $pageCnt";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $userId]);
		
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
	
	//READ STORY
	function recentlyList($userId, $pageNum, $pageCnt){
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
			recentlyList
			inner join story on recentlyList.storyId = story.storyId
			inner join user on story.userId = user.userId
			left join publicationUser on story.userId = publicationUser.userId
			left join publication on publicationUser.publicationId = publication.publicationId
			left join topic on story.topicId = topic.topicId
		WHERE
			recentlyList.userId = ? AND user.del = 'N' AND story.del = 'N'
		ORDER BY
			recentlyList.recentlyAt DESC
		LIMIT
			$pageNum, $pageCnt";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $userId, $userId]);
		
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
			$st->execute([$storyId['storyId']]);
			
			$st->setFetchMode(PDO::FETCH_ASSOC);
			$contents = $st->fetchAll();
			$res[$cnt]['text'] = $contents[0]['SmallText'];
			
			$pdo = pdoSqlConnect();
			$query = 
			"SELECT
				contents.contents as image
			FROM
				contents
			WHERE
				contents.storyId = ?  AND contents.type = 'image'
			ORDER BY
				sequence
			LIMIT 1";

			$st = $pdo->prepare($query);
			$st->execute([$storyId['storyId']]);
			
			$st->setFetchMode(PDO::FETCH_ASSOC);
			$contents = $st->fetchAll();
			$res[$cnt]['image'] = $contents[0]['image'];
			
			$cnt++;
		}
		
		$st = null;
		$pdo = null;
		
		return $res;
	}
	
	//READ Popular STORY
	function popularStory($userId){
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
			user.del = 'N' AND story.del = 'N'
		ORDER BY
			(SELECT sum(cnt)FROM storyClap WHERE storyId = story.storyId)  DESC
		LIMIT
			5";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $userId]);
		
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