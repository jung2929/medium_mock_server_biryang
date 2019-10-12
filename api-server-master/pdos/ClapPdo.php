<?php
	//CLAP USER
	function addClap($userId, $storyId){
		$pdo = pdoSqlConnect();
		$query =
		"SELECT
			storyClap.cnt
		FROM
			storyClap
		WHERE
			storyClap.userId = ? AND storyClap.storyId = ?";

		$st = $pdo->prepare($query);
		$st->execute([$userId, $storyId]);
		
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res  = $st->fetchAll();
		
		
		if(empty($res)) {
			$query =
			"INSERT INTO
				storyClap(userId, storyId)
			VALUES
				(?, ?)";
				
			$st = $pdo->prepare($query);
			$st->execute([$userId, $storyId]);
			
			$res[0]['cnt'] = 1;
		}
		else if($res[0]['cnt'] < 50){
			$query =
			"UPDATE
				storyClap
			SET
				cnt = cnt + 1
			WHERE
				userId = ? AND storyId =?";
				
			$st = $pdo->prepare($query);
			$st->execute([$userId, $storyId]);
			
			$res[0]['cnt'] = $res[0]['cnt'] + 1;
		}
		
		
		$st = null;
		$pdo = null;
		
		return $res[0]['cnt'];
	}
	
