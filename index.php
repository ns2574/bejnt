<html>
<head>

</head>
<body>
	<a>
		<h1>Register!:</h1>
		<form action="adduser.php" method="post">
			telephone: <br><input type="text" name="telephone"><br>
			latitude: <br><input type="text" name="latitude"><br>
			longitude: <br><input type="text" name="longitude"><br>
			<input type="submit">
		</form>
	</a>
	<a>
		<h1>Create Emergency!:</h1>
		<form action="createemergency.php" method="post">
			telephone: <br><input type="text" name="telephone"><br>
			<input type="submit">
		</form>
	</a>
	<a>
		<h1>Update!:</h1>
		<form action="update.php" method="post">
			telephone: <br><input type="text" name="telephone"><br>
			latitude: <br><input type="text" name="latitude"><br>
			longitude: <br><input type="text" name="longitude"><br>
			<input type="submit">
		</form>
	</a>
	<a>
		<h1>Help Emergency!:</h1>
		<form action="helpemergency.php" method="post">
			telephone: <br><input type="text" name="telephone"><br>
			damselhash: <br><input type="text" name="damsel"><br>
			timestamp: <br><input type="text" name="timestamp"><br>
			<input type="submit">
		</form>
	</a>
	<a>
		<h1>Unhelp!:</h1>
		<form action="unhelp.php" method="post">
			telephone: <br><input type="text" name="telephone"><br>
			damselhash: <br><input type="text" name="damsel"><br>
			timestamp: <br><input type="text" name="timestamp"><br>
			<input type="submit">
		</form>
	</a>
	<a>
		<h1>Report!:</h1>
		<form action="reportemergency.php" method="post">
			telephone: <br><input type="text" name="telephone"><br>
			damselhash: <br><input type="text" name="damsel"><br>
			timestamp: <br><input type="text" name="timestamp"><br>
			reason: <br><input type="text" name="reason"><br>
			<input type="submit">
		</form>
	</a>
</body>
</html>