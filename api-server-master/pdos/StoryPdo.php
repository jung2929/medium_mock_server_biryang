<?php
	//CREATE USER
	function addAllStory($userId, $title, $subTitle, $topicId, $contents){
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO story (userId, title) VALUES ( ? , ?)";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $title]);
		
		$st = null;
		$pdo = null;		
		
		$pdo = pdoSqlConnect();
		$query = "SELECT storyId FROM story WHERE userId = ? AND title = ? ORDER BY createAt DESC LIMIT 1";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $title]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$storyId =$res[0]['storyId'];
		
		
		$paragraphSet = explode("/$#paragraph=", $contents); 

		foreach($paragraphSet as $paragraphContents) {
			$pdo = pdoSqlConnect();
			$query = "INSERT INTO sequence (storyId) VALUES (?)";
			
			$st = $pdo->prepare($query);
			$st->execute([$storyId]);
			
			$pdo = pdoSqlConnect();
			$query = "SELECT sequenceId FROM sequence WHERE storyId = ? ORDER BY sequenceId DESC LIMIT 1";
			
			$st = $pdo->prepare($query);
			$st->execute([$storyId]);
			
			$st->setFetchMode(PDO::FETCH_ASSOC);
			$res = $st->fetchAll();
			$sequenceId =$res[0]['sequenceId'];
			
			$textSet = explode("/$#text=", $paragraphContents); 
			if(!empty($textSet[1])) {
				$pdo = pdoSqlConnect();
				$query = "INSERT INTO text (storyId, sequenceId, text) VALUES (?, ?, ?)";
				
				$st = $pdo->prepare($query);
				$st->execute([$storyId, $sequenceId, $textSet[1]]);
			}
			
			$textListSet = explode("/$#textList=", $paragraphContents); 
			if(!empty($textListSet[1])) {
				$pdo = pdoSqlConnect();
				$query = "INSERT INTO textList (storyId, sequenceId, textList) VALUES (?, ?, ?)";
				
				$st = $pdo->prepare($query);
				$st->execute([$storyId, $sequenceId, $textListSet[1]]);
			}
			
			$imageSet = explode("/$#image=", $paragraphContents); 
			if(!empty($imageSet[1])) {
				$pdo = pdoSqlConnect();
				$query = "INSERT INTO image (storyId, sequenceId, image) VALUES (?, ?, ?)";
				
				$st = $pdo->prepare($query);
				$st->execute([$storyId, $sequenceId, $imageSet[1]]);
			}
			
			$quotationSet = explode("/$#quotation=", $paragraphContents); 
			if(!empty($quotationSet[1])) {
				$pdo = pdoSqlConnect();
				$query = "INSERT INTO quotation (storyId, sequenceId, quotation) VALUES (?, ?, ?)";
				
				$st = $pdo->prepare($query);
				$st->execute([$storyId, $sequenceId, $quotationSet[1]]);
			}
			
			$delimiterSet = explode("/$#delimiter=", $paragraphContents); 
			if(!empty($delimiterSet[1])) {
				$pdo = pdoSqlConnect();
				$query = "INSERT INTO delimiter (storyId, sequenceId, delimiter) VALUES (?, ?, ?)";
				
				$st = $pdo->prepare($query);
				$st->execute([$storyId, $sequenceId, $delimiterSet[1]]);
			}
		}		

		$st = null;
		$pdo = null;
	}
	
	//스토리 추가
	function addStory($userId, $title, $subTitle, $topicId){
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO story (userId, title, subTitle, topicId) VALUES ( ? , ?, ?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$userId, $title, $subTitle, $topicI]);
		
		$st = null;
		$pdo = null;		
		
		
		$pdo = pdoSqlConnect();
		$query = "SELECT storyId FROM story WHERE userId = ? AND title = ? ORDER BY createAt DESC LIMIT 1";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $title]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		
		return $res[0]['storyId'];
	}
	
	//내용 추가
	function addContents($storyId, $sequence, $type, $contents){
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO contents (storyId, sequence, type, contents) VALUES (?, ?, ?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId, $sequence, $type, $contents]);
		
		$st = null;
		$pdo = null;
	}
	
	//READ STORY
	function readStory($storyId){
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
			(SELECT readingList.id FROM readingList WHERE readingList.del = 'N' AND readingList.storyId = story.storyId) as readingListId,
			(SELECT readingList.type FROM readingList WHERE readingList.del = 'N' AND readingList.storyId = story.storyId) as readingType,
			(SELECT sum(cnt)FROM storyClap WHERE storyId = story.storyId) as clapCnt,
			story.createAt
		FROM
			story
			inner join user on story.userId = user.userId
			left join publicationUser on story.userId = publicationUser.userId
			left join publication on publicationUser.publicationId = publication.publicationId
			left join topic on story.topicId = topic.topicId
		WHERE
			storyId = ? AND user.del = 'N' AND story.del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			contents.contentsId,
			contents.sequence,
			contents.type,
			contents.contents
		FROM
			contents
		WHERE
			contents.storyId = ?
		ORDER BY
			sequence";

		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res[0]['contentsList'] = $st->fetchAll();
	
		$st = null;
		$pdo = null;
		
		return $res[0];
	}
	
	//중복 확인
	function overlapRecentlyList($userId, $storyId){
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			recentlyList.id
		FROM
			recentlyList
		WHERE
			recentlyList.userId = ? AND recentlyList.storyId = ?";
		
		$st = $pdo->prepare($query);
		$st->execute([$userId, $storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		
		$st = null;
		$pdo = null;
		
		return $res;
	}
	
	//최근 읽은 게시글
	function addRecentlyList($userId, $storyId){
		$pdo = pdoSqlConnect();
		$query =
		"INSERT INTO
			recentlyList (userId, storyId)
		VALUES
			(?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$userId, $storyId]);
		
		$st = null;
		$pdo = null;
	}

	//최근 읽은 게시글 업데이트
	function updateRecentlyList($userId, $storyId){
		$recentlyAt = date("Y-m-d h:i:s", time());
		
		$pdo = pdoSqlConnect();
		$query =
		"UPDATE
			recentlyList
		SET
			recentlyList.recentlyAt = ?
		WHERE
			recentlyList.userId = ? AND recentlyList.storyId = ?";
		
		$st = $pdo->prepare($query);
		$st->execute([$recentlyAt, $userId, $storyId]);
		
		$st = null;
		$pdo = null;
	}
	
	//DELETE STORY USER
	function deleteStory($userId, $storyId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE story SET del = 'Y' WHERE userId =? AND storyId = ?";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $storyId]);

		$st = null;
		$pdo = null;
	}