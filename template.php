<?php 
	//This is for registering
	
	//Database Start
	try
	{
		$link = new PDO('pass.czwtincxuane.us-west-2.rds.amazonaws.com:5432;dbname=PASS', 'ex221', 'l6u6m6i1n3ascendant');
	}
	catch (PDOException $e) 
	{
		die('1: ' . $e->getMessage());
	}
	//Get HTTPS response ready

	//Getting values
	session_start();
	
	//Check parameters
	
	//Get HTTPS response ready
	/*
		Parameter 1: parameterone
		Parameter 2: parametertwo
		Parameter 3: parameterthr
		Parameter 4: parameterfou
		Parameter 5: parameterfri
		Parameter 6: parametersix
		Actual Task: taskTask
		FailedSelect: failedSelectParam
		
	*/
	$problem = 0;
	if((strlen($email) > 32) or (strlen($password) > 32) or (strlen($f_name) > 32) or (strlen($l_name) > 32) or (!is_int($student)) or ($student != 1 AND $student != 0) or !(filter_var($email, FILTER_VALIDATE_EMAIL)))
	{
		die ("Failed due to bad parameters\n");
	}
	if(!isset($_POST["email"]) || empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
	{
		$problem = 1;
	}
	if(!isset($_POST["password"]) || empty($_POST["password"]) || strlen($_POST["password"]) < 8 || strlen($_POST["password"]) > 32)
	{
		$problem = 2;
	}
	if(!isset($_POST["f_name"]) || empty($_POST["f_name"]) || strlen($_POST["f_name"]) < 8 || strlen($_POST["f_name"]) > 32)
	{
		$problem = 3;
	}
	if(!isset($_POST["l_name"]) || empty($_POST["l_name"]) || strlen($_POST["l_name"]) < 8 || strlen($_POST["l_name"]) > 32)
	{
		$problem = 4;
	}
	if(!isset($_POST["student"]) || empty($_POST["student"]) || ($_POST["student"] != 1 && $_POST["student"] != 0)  || !is_numeric($_POST["student"]))
	{
		$problem = 5;
	}
	if($problem > 0)
	{
		die($problem. ": Invalid Parameter!");
	}
	
	$email = htmlspecialchars($_POST["email"]);
	$password = htmlspecialchars($_POST["password"]);
	$f_name = htmlspecialchars($_POST["f_name"]);
	$l_name = htmlspecialchars($_POST["l_name"]);
	$student = (int) htmlspecialchars($_POST["student"]);
	
	try
	{
		$link->beginTransaction();
		
		//Check if exists in actual users
		$result = $link->prepare("SELECT email FROM user_info WHERE email = ?;");
		$success = $result -> execute(array($email));
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
			die("7: User already exists!");
		}
			
		//Check if user exists in temp section
		$result = null;			
		$result = $link->prepare("SELECT email FROM temp_users WHERE email = ?;");
		$success = $result -> execute(array($email));	
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("8: ".$error);
		}
		else if($result->rowCount() != 0)
		{
			$link->rollBack();
			$result = null;
			$link = null;
			die("9: User already exists!");
		}
			
		//Hashing Time
		$hashword = password_hash($password, PASSWORD_BCRYPT);
		
		//Create transaction id and remake if needed
		$transaction_cookie = '';
		do
		{
			$transaction = '';
			do
			{
				$transaction = bin2hex(openssl_random_pseudo_bytes(128, $secure));
			}
			while(!$secure);
			
			//Check if transaction id is valid
			$result = null;
			$result = $link->prepare("SELECT transaction_cookie FROM temp_users WHERE transaction_cookie = ?;");
			$success = $result -> execute(array($transaction));
			if(!$success)
			{
				$link->rollBack();
				$error = implode($result->errorInfo());
				$result = null;
				$link = null;	
				die("10: ".$error);
			}
			$transaction_cookie = $transaction;
		}
		while($result->rowCount() != 0);
			
		//Make confirmation code
		$code ='';
		do
		{
			$transaction = bin2hex(openssl_random_pseudo_bytes(4, $secure));
			$code =$transaction;
		}
		while(!$secure);
		
		//Insertion
		$result = null;
		$result = $link->prepare("INSERT INTO temp_users (fname, lname, email, password, student, transaction_cookie, code) VALUES (?, ?, ?, ?, ?, ?, ?);");
		$success = $result -> execute(array($f_name, $l_name, $email, $hashword, $student, $transaction_cookie, $code));
		if(!$success)
		{
			$link->rollBack();
			$error = implode($result->errorInfo());
			$result = null;
			$link = null;	
			die("11: ".$error);
		}
		else
		{
			$result = null;
		}

		if(mail($email, 'Confirmation Code For Pass', 'Hi, this your confirmation code for pass: '.$code.".\r\nWelcome, to PASS. If you did not register with us please disregard this email.\r\n\r\nThank You,\r\nThe PASS Team","From: doNotReplyPASS@passus.poly.edu"))
		{
			echo $transaction_cookie;
		}
		else
		{
			echo "12: Email Failed";
		}
				
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