<?php 
	//This is for reporting a emergency
	
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
	if(!isset($_POST["reason"]) || empty($_POST["reason"]) || (!is_numeric($_POST["reason"]) && !filter_input(INPUT_POST, 'reason', FILTER_VALIDATE_INT)))
	{
		$problem = 4;
	}
	if($problem > 0)
	{
		die($problem. ": Invalid Parameter!");
	}
	
	$telephone = (int) htmlspecialchars($_POST["telephone"]);
	$damsel = htmlspecialchars($_POST["damsel"]);
	$timestamp = htmlspecialchars($_POST["timestamp"]);
	$reason = (int) htmlspecialchars($_POST["reason"]);
	
	try
	{
		$link->beginTransaction();
		
		//Check if exists in actual users are in a help request and is active
		$result = $link->prepare("SELECT emergency.damsel FROM helpers, emergency WHERE emergency.time = helpers.time AND emergency.damsel = helpers.damsel AND helpers.helper = ? AND emergency.damselhash = ? AND emergency.time = ? AND ? >= ? AND helpers.active = 1;");
		$success = $result -> execute(array($telephone, $damsel, $timestamp, $timestamp, date('Y-m-d H:i:s', time() - 10 * 60)));
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("5: ".$error);
		}
		if($result->rowCount() != 1)
		{
			$link->rollBack();
			$result = null;
			$link = null;
			die("6: You weren't picked to help!");
		}
		
		$row = $result -> fetch(PDO::FETCH_NUM);
		$damselphone = $row[0];
		
		//Lets update to report this
		$result = $link->prepare("INSERT INTO report VALUES (?, ?, ? , ?)");
		$success = $result -> execute(array($damselphone, $telephone, $timestamp, $reason));
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("7: ".$error);
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