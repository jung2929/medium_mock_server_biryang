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
	
	//텍스트 추가
	function addText($storyId, $text){
		$pdo = pdoSqlConnect();
		//순서 추가
		$query = "INSERT INTO sequence (storyId) VALUES (?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$pdo = pdoSqlConnect();
		//순서 확인
		$query = "SELECT sequenceId FROM sequence WHERE storyId = ? ORDER BY sequenceId DESC LIMIT 1";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$sequenceId =$res[0]['sequenceId'];
		
		$pdo = pdoSqlConnect();
		//글 추가
		$query = "INSERT INTO text (sequenceId, text) VALUES (?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$sequenceId, $text]);
		
		$st = null;
		$pdo = null;
	}
	
	//리스트 추가
	function addTextList($storyId, $textList){
		$pdo = pdoSqlConnect();
		//순서 추가
		$query = "INSERT INTO sequence (storyId) VALUES (?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$pdo = pdoSqlConnect();
		//순서 확인
		$query = "SELECT sequenceId FROM sequence WHERE storyId = ? ORDER BY sequenceId DESC LIMIT 1";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$sequenceId =$res[0]['sequenceId'];
		
		$pdo = pdoSqlConnect();
		//글 추가
		$query = "INSERT INTO textList (sequenceId, textList) VALUES (?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$sequenceId, $textList]);		
		
		$st = null;
		$pdo = null;
	}
	
	//이미지 추가
	function addImage($storyId, $image){
		$pdo = pdoSqlConnect();
		//순서 추가
		$query = "INSERT INTO sequence (storyId) VALUES (?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$pdo = pdoSqlConnect();
		//순서 확인
		$query = "SELECT sequenceId FROM sequence WHERE storyId = ? ORDER BY sequenceId DESC LIMIT 1";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$sequenceId =$res[0]['sequenceId'];
		
		$pdo = pdoSqlConnect();
		//글 추가
		$query = "INSERT INTO image (sequenceId, image) VALUES (?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$sequenceId, $image]);		
		
		$st = null;
		$pdo = null;
	}
	
	//인용구 추가
	function addQuotation($storyId, $quotation){
		$pdo = pdoSqlConnect();
		//순서 추가
		$query = "INSERT INTO sequence (storyId) VALUES (?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$pdo = pdoSqlConnect();
		//순서 확인
		$query = "SELECT sequenceId FROM sequence WHERE storyId = ? ORDER BY sequenceId DESC LIMIT 1";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$sequenceId =$res[0]['sequenceId'];
		
		$pdo = pdoSqlConnect();
		//글 추가
		$query = "INSERT INTO quotation (sequenceId, quotation) VALUES (?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$sequenceId, $quotation]);		
		
		$st = null;
		$pdo = null;
	}
	
	//구분자 추가
	function addDelimiter($storyId, $delimiter){
		$pdo = pdoSqlConnect();
		//순서 추가
		$query = "INSERT INTO sequence (storyId) VALUES (?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$pdo = pdoSqlConnect();
		//순서 확인
		$query = "SELECT sequenceId FROM sequence WHERE storyId = ? ORDER BY sequenceId DESC LIMIT 1";
		
		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$sequenceId =$res[0]['sequenceId'];
		
		$pdo = pdoSqlConnect();
		//글 추가
		$query = "INSERT INTO delimiter (sequenceId, delimiter) VALUES (?, ?)";
		
		$st = $pdo->prepare($query);
		$st->execute([$sequenceId, $delimiter]);		
		
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
			story.createAt
		FROM
			story
			inner join user on story.userId = user.userId
			left join topic on story.topicId = topic.topicId
		WHERE
			storyId = ? AND user.del = 'N' AND story.del = 'N'";

		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		
		//text 조회
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			text.sequenceId,
			text.text
		FROM
			text
			inner join sequence on text.sequenceId = sequence.sequenceId
		WHERE
			sequence.storyId = ?
		ORDER BY
			text.sequenceId";

		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res[0]['text'] = $st->fetchAll();
		
		//textList 조회
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			textList.sequenceId,
			textList.textList
		FROM
			textList
			inner join sequence on textList.sequenceId = sequence.sequenceId
		WHERE
			sequence.storyId = ?
		ORDER BY
			textList.sequenceId";

		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res[0]['textList'] = $st->fetchAll();
		
		//image 조회
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			image.sequenceId,
			image.image
		FROM
			image
			inner join sequence on image.sequenceId = sequence.sequenceId
		WHERE
			sequence.storyId = ?
		ORDER BY
			image.sequenceId";

		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res[0]['image'] = $st->fetchAll();
		
		//quotation 조회
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			quotation.sequenceId,
			quotation.quotation
		FROM
			quotation
			inner join sequence on quotation.sequenceId = sequence.sequenceId
		WHERE
			sequence.storyId = ?
		ORDER BY
			quotation.sequenceId";

		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res[0]['quotation'] = $st->fetchAll();
		
		//delimiter 조회
		$pdo = pdoSqlConnect();
		$query = 
		"SELECT
			delimiter.sequenceId,
			delimiter.delimiter
		FROM
			delimiter
			inner join sequence on delimiter.sequenceId = sequence.sequenceId
		WHERE
			sequence.storyId = ?
		ORDER BY
			delimiter.sequenceId";

		$st = $pdo->prepare($query);
		$st->execute([$storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res[0]['delimiter'] = $st->fetchAll();
		
		return $res[0];
	}
	
	//DELETE STORY USER
	function deleteStory($storyId){
		$pdo = pdoSqlConnect();
		$query = "UPDATE story SET del = 'Y' WHERE storyId = ?";

		$st = $pdo->prepare($query);
		$st->execute([$storyId]);

		$st = null;
		$pdo = null;
	}