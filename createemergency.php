<?php 
	//This is for registering
	
	//Database Start
	try
	{
		$link = new PDO('pgsql:host=pass.czwtincxuane.us-west-2.rds.amazonaws.com;port=5432;dbname=buddysystem', $_SERVER['DB_USER'], $_SERVER['DB_PASS']);
	}
	catch (PDOException $e) 
	{
		die('0: ' . $e->getMessage());
	}
	//Get HTTPS response ready

	//Getting values
	session_start();
	
	//Check parameters
	
	//Get HTTPS response ready
	/*
		Parameter 1: telephone
	*/
	$problem = 0;
	if(!isset($_POST["telephone"]) || empty($_POST["telephone"]) || !is_numeric($_POST["telephone"]) || strlen($_POST["telephone"]) != 10)
	{
		$problem = 1;
	}
	if($problem > 0)
	{
		die($problem. ": Invalid Parameter!");
	}
	
	$telephone = (int) htmlspecialchars($_POST["telephone"]);
	
	try
	{
		$link->beginTransaction();
		
		//Check if exists in actual users
		$result = $link->prepare("SELECT latitude, longitude FROM user_info WHERE telephone = ?;");
		$success = $result -> execute(array($telephone));
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("2: ".$error);
		}
		if($result->rowCount() != 1)
		{
			$link->rollBack();
			$result = null;
			$link = null;
			die("3: User doesn't exist!");
		}
		
		$row = $result -> fetch(PDO::FETCH_NUM);
		$latitude = $row[0];
		$longitude = $row[1];
		
		//Lets just make sure we aren't a active responder
		$result = $link->prepare("SELECT * FROM helpers WHERE helper = ? AND time >= ? AND active = 1;");
		$success = $result -> execute(array($telephone, date('Y-m-d H:i:s', time() - /*10 * 60*/ 1 * 1)));
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("4: ".$error);
		}
		if($result->rowCount() != 0)
		{
			$link->rollBack();
			$result = null;
			$link = null;	
			die("5: "."Can't get help when you are a helper.");
		}
		
		//Lets just make sure there wasn't a emergency during our grace period
		$result = $link->prepare("SELECT time FROM emergency WHERE damsel = ? AND time >= ?;");
		$success = $result -> execute(array($telephone, date('Y-m-d H:i:s', time() - /*10 * 60*/ 1 * 1)));
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("6: ".$error);
		}
		if($result->rowCount() != 0)
		{
			$link->rollBack();
			$result = null;
			$link = null;	
			die("7: "."Tried to ask for help during grace period.");
		}
		
		//Lets create a emergency
		$result = null;			
		$result = $link->prepare("INSERT INTO emergency (damsel, damselhash)VALUES(?, ?);");
		$success = $result -> execute(array($telephone, password_hash($telephone, PASSWORD_DEFAULT)));	
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("8: ".$error);
		}
		
		//Lets grab the timestamp too
		$result = null;			
		$result = $link->prepare("SELECT time FROM emergency WHERE damsel = ? ORDER BY time DESC;");
		$success = $result -> execute(array($telephone));	
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("9: ".$error);
		}
		if($result->rowCount() == 0)
		{
			$link->rollBack();
			$result = null;
			$link = null;	
			die("10: "."Did not find any added values!");
		}
		$row = $result -> fetch(PDO::FETCH_NUM);
		$timestamp = $row[0];
		
		//Now we use haversine to process
		$longitude = deg2rad($longitude);
		$latitude = deg2rad($latitude);
		
		//Rotational Constant
		$r = 6371000.0;
		
		//Distance to set in
		$distance = 1000.0;
		
		//This is done for easier reading (kinda)
		$cosdisdivr  = cos($distance/$r);
		
		//We use latitude to get a rough calculation 
		$sinlatsqu = sin($latitude) * sin($latitude);
		$coslatsqu = cos($latitude) * cos($latitude);
		
		$cosminussls = $cosdisdivr - $sinlatsqu;
		
		$cmsdivcls = $cosminussls/$coslatsqu;
		
		$bound = rad2deg(acos($cmsdivcls));
		
		//We got the rough calculation, time to get the bounds
		
		$longitude = rad2deg($longitude);
		$latitude = rad2deg($latitude);
		
		$latplus = $latitude + $bound;
		$latminus = $latitude - $bound;
		$longplus = $longitude + $bound;
		$longminus = $longitude - $bound;
		
		//Grab anyone in range that can help
		$result = null;			
		$result = $link->prepare("SELECT telephone FROM user_info WHERE latitude <= ? AND latitude >= ? AND longitude <= ? AND longitude >= ? AND telephone != ?;");
		$success = $result -> execute(array($latplus, $latminus, $longplus, $longminus, $telephone));	
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("11: ".$error);
		}
		
		$nearby = $result -> fetchAll();
		for($i = 0; $i < sizeof($nearby); $i++)
		{
			//Add all helpers to help requests
			$result = null;
			$result = $link->prepare("INSERT INTO helprequest VALUES (?, ?, ?);");
			$success = $result -> execute(array($telephone, $nearby[$i][0], $timestamp));	
			if(!$success)
			{
				$link->rollBack();
				$error = implode($result->errorInfo());
				$result = null;
				$link = null;
				die("12: ".$error);
			}
		}

		echo "Confirm";
		//Commit time
		$link -> commit();
	}
	catch(Exception $e)
	{
		$link -> rollback();
		echo "13 ".$e->getMessage();
	}
	
	//Kill the link
	$link = null;
?>