<?php
	//CREATE USER
	function addStory($userId, $title, $subTitle, $topicId, $contents){
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
		
		
		/*
		$contents=
		"/$#paragraph=/$#text=sasddfsdfsdf
		/$#paragraph=/$#delimiter=123
		/$#paragraph=/$#text=sdfds
		";*/
		
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
	