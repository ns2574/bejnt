<?php 
	//This is for updating
	
	//Database Start
	try
	{
		$link = new PDO('pgsql:host=pass.czwtincxuane.us-west-2.rds.amazonaws.com;port=5432;dbname=buddysystem',  $_SERVER['DB_USER'], $_SERVER['DB_PASS']);
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
		Parameter 2: latitude
		Parameter 3: longitude
	*/
	$problem = 0;
	if(!isset($_POST["telephone"]) || empty($_POST["telephone"]) || !is_numeric($_POST["telephone"]) || strlen($_POST["telephone"]) != 10)
	{
		$problem = 1;
	}
	if(!isset($_POST["latitude"]) || empty($_POST["latitude"]) || (!is_numeric($_POST["latitude"]) && !filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT)) || floatval($_POST["latitude"]) > 90 || floatval($_POST["latitude"]) < -90)
	{
		$problem = 2;
	}
	if(!isset($_POST["longitude"]) || empty($_POST["longitude"]) || (!is_numeric($_POST["longitude"]) && !filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT)) || floatval($_POST["longitude"]) > 180 || floatval($_POST["longitude"]) < -180)
	{
		$problem = 3;
	}
	if($problem > 0)
	{
		die($problem. ": Invalid Parameter!");
	}
	
	$telephone = (int) htmlspecialchars($_POST["telephone"]);
	$latitude = (float) htmlspecialchars($_POST["latitude"]);
	$longitude = (float) htmlspecialchars($_POST["longitude"]);
	
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
			die("4: ".$error);
		}
		if($result->rowCount() != 1)
		{
			$link->rollBack();
			$result = null;
			$link = null;
			die("5: User doesn't exist!");
		}
		
		//Update actual location
		$result = $link->prepare("UPDATE user_info SET latitude = ?, longitude = ? WHERE telephone = ?;");
		$success = $result -> execute(array($latitude, $longitude, $telephone));
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("6: ".$error);
		}
		
		//Time to grab actual value of people we are helping
		$result = $link->prepare("SELECT user_info.latitude, user_info.longitude FROM user_info, helpers WHERE user_info.telephone = helpers.damsel AND time >= ? AND helpers.helper = ? AND helpers.active = 1;");
		$success = $result -> execute(array(date('Y-m-d H:i:s', time() - 10 * 60), $telephone));
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("7: ".$error);
		}
		if($result->rowCount() > 1)
		{
			$link->rollBack();
			$result = null;
			$link = null;	
			die("8: "."You are somehow helping multiple people.");
		}
		
		$areHelping =  "{0,0,0}";
		$helpedby = "{0}";
		if($result->rowCount() == 1)
		{
			$recordedResult = $result -> fetch(PDO::FETCH_NUM);
			$areHelping =  "{1,".$recordedResult[0].",".$recordedResult[1]."}";
		}
		else
		{
			$helpedby = "{";
			//Lets grab # people that are helping us.
			$result = null;			
			$result = $link->prepare("SELECT count(helper) FROM helpers WHERE damsel = ? AND time >= ?;");
			$success = $result -> execute(array($telephone, date('Y-m-d H:i:s', time() - 10 * 60)));	
			if(!$success)
			{
				$link->rollBack();
				$error = implode($result->errorInfo());
				$result = null;
				$link = null;	
				die("9: ".$error);
			}
			if($result->rowCount() != 1)
			{
				$link->rollBack();
				$error = implode($result->errorInfo());
				$result = null;
				$link = null;	
				die("10: "."Somehow didn't get a result");
			}
			else
			{
				$row = $result -> fetch(PDO::FETCH_NUM); 
				$helpedby = $helpedby . $row[0];
			}
			$helpedby = $helpedby . "}";
		}
		
		//Time to grab actual values of people you could be helping
		$result = null;			
		$result = $link->prepare("SELECT helprequest.time, emergency.damselhash FROM helprequest, emergency  WHERE helprequest.damsel = emergency.damsel AND helprequest.time = emergency.time AND helprequest.helper = ? AND helprequest.time >= ?");
		$success = $result -> execute(array($telephone, date('Y-m-d H:i:s', time() - 10 * 60)));	
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("11: ".$error);
		}
		
		$damsals = $result -> fetchAll();
		$damsalsbrackets = "{";
		for($i = 0; $i < sizeof($damsals); $i++)
		{
			$damsalsbrackets = $damsalsbrackets . "{" . $damsals[$i][0] . ", " . $damsals[$i][1] . "}";
			if($i != sizeof($damsals) - 1)
			{
				$damsalsbrackets = $damsalsbrackets . ",";
			}
		}
		$damsalsbrackets = $damsalsbrackets . "}";
		
		echo $areHelping.$helpedby.$damsalsbrackets;
		//Commit time
		$link -> commit();
	}
	catch(Exception $e)
	{
		$link -> rollback();
		echo "12 ".$e->getMessage();
	}
	
	//Kill the link
	$link = null;
?>