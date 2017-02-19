<?php 
	//This is for helping in a emergency
	
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
		Parameter 2: hash of damsel
		Parameter 3: timestamp
	*/
	$problem = 0;
	if(!isset($_POST["telephone"]) || empty($_POST["telephone"]) || !is_numeric($_POST["telephone"]) || strlen($_POST["telephone"]) != 10)
	{
		$problem = 1;
	}
	if(!isset($_POST["damsel"]) || empty($_POST["damsel"]))
	{
		$problem = 2;
	}
	if(!isset($_POST["timestamp"]) || empty($_POST["timestamp"]) || !date($_POST["timestamp"]))
	{
		$problem = 3;
	}
	if($problem > 0)
	{
		die($problem. ": Invalid Parameter!");
	}
	
	$telephone = (int) htmlspecialchars($_POST["telephone"]);
	$damsel = htmlspecialchars($_POST["damsel"]);
	$timestamp = htmlspecialchars($_POST["timestamp"]);
	
	try
	{
		$link->beginTransaction();
		
		//Check if exists in actual users are in a help request
		$result = $link->prepare("SELECT emergency.damsel FROM helprequest, emergency WHERE emergency.time = helprequest.time AND emergency.damsel = helprequest.damsel AND helprequest.helper = ? AND emergency.damselhash = ? AND emergency.time = ? AND ? >= ?;");
		$success = $result -> execute(array($telephone, $damsel, $timestamp, $timestamp, date('Y-m-d H:i:s', time() - 10 * 60)));
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
			die("5: You weren't picked to help!");
		}
		
		$row = $result -> fetch(PDO::FETCH_NUM);
		$damselphone = $row[0];
		
		//Helpers can't be needing help too
		$result = $link->prepare("SELECT * FROM emergency WHERE damsel = ? AND time >= ?;");
		$success = $result -> execute(array($telephone, date('Y-m-d H:i:s', time() - 10 * 60)));
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
			die("7: You can't help if you need help right now!");
		}
		
		//Helpers can't help multiple people
		$result = $link->prepare("SELECT * FROM helpers WHERE helper = ? AND time >= ? AND active = 1;");
		$success = $result -> execute(array($telephone, date('Y-m-d H:i:s', time() - 10 * 60)));
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("8: ".$error);
		}
		if($result->rowCount() != 0)
		{
			$link->rollBack();
			$result = null;
			$link = null;
			die("9: You can't help if you need help right now!");
		}
		
		//If we already helped you before and it's still valid
		$result = $link->prepare("SELECT * FROM helpers WHERE helper = ? AND time >= ? AND active = 0 AND time = ?;");
		$success = $result -> execute(array($telephone, date('Y-m-d H:i:s', time() - 10 * 60), $timestamp));
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("10: ".$error);
		}
		if($result->rowCount() != 0)
		{
			//Lets update to fix this
			$result = $link->prepare("UPDATE helpers SET active = 1 WHERE helper = ? AND time >= ? AND active = 0;");
			$success = $result -> execute(array($telephone, date('Y-m-d H:i:s', time() - 10 * 60)));
			if(!$success)
			{
				$link->rollBack();
				$error = implode($result->errorInfo());
				$result = null;
				$link = null;	
				die("11: ".$error);
			}
		}
		else
		{
			//Okay promote them to helping
			$result = $link->prepare("INSERT INTO helpers VALUES (?, ?, ?, 1);");
			$success = $result -> execute(array($damselphone, $telephone, $timestamp));
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