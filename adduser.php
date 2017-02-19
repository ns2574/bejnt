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
		$result = $link->prepare("SELECT telephone FROM user_info WHERE telephone = ?;");
		$success = $result -> execute(array($telephone));
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
			die("5: User already exists!");
		}
			
		//Check if user exists in temp section
		$result = null;			
		$result = $link->prepare("INSERT INTO user_info VALUES(?, ?, ?);");
		$success = $result -> execute(array($telephone, $latitude, $longitude));	
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("6: ".$error);
		}
		
		echo "Confirm";
		//Commit time
		$link -> commit();
	}
	catch(Exception $e)
	{
		$link -> rollback();
		echo "7 ".$e->getMessage();
	}
	
	//Kill the link
	$link = null;
?>