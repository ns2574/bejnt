<?php 
	//This is for unhelping
	
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
	$timestamp = htmlspecialchars($_POST["timestamp"]);
	$damsel = htmlspecialchars($_POST["damsel"]);
	
	try
	{
		$link->beginTransaction();
		
		//Check if exists in actual users are in a help request
		$result = $link->prepare("SELECT helpers.active FROM helpers, emergency WHERE helpers.time = emergency.time AND helpers.damsel = emergency.damsel AND helpers.active = 1 AND helpers.helper = ? AND helpers.time = ? AND helpers.time >= ? AND emergency.damselhash = ?;");
		$success = $result -> execute(array($telephone, $timestamp, date('Y-m-d H:i:s', time() - 10 * 60), $damsel));
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
		$active = (int)$row[0];
		if($active == 1)
		{
			$result = $link->prepare("UPDATE helpers AS h SET active = 0 FROM emergency AS e WHERE e.time = h.time AND e.damsel = h.damsel AND h.active = 1 AND h.helper = ? AND h.time = ? AND h.time >= ? AND e.damselhash = ?;");
			$success = $result -> execute(array($telephone, $timestamp, date('Y-m-d H:i:s', time() - 10 * 60), $damsel));
			if(!$success)
			{
				$link->rollBack();
				$error = implode($result->errorInfo());
				$result = null;
				$link = null;	
				die("6: ".$error);
			}
		}
		else if($active == 0)
		{
			$link->rollBack();
			$result = null;
			$link = null;
			die("7: You decided to not help already!");
		}
		
		echo "Confirm";
		//Commit time
		$link -> commit();
	}
	catch(Exception $e)
	{
		$link -> rollback();
		echo "8: ".$e->getMessage();
	}
	
	//Kill the link
	$link = null;
?>