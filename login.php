<?php session_start(); ?>

<!DOCTYPE html>

<html class="other">

<head>
	<meta charset="UTF-8">
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
	<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	if ($mysqli->errno){
		print('There was a mysqli error');
		print($mysqli->error);
		exit();
	}

	 ?>
</head>

<body class="homepage">
	<?php include 'albumHeader.php'; ?>
	
	<?php
	$username = filter_input(INPUT_POST, 'Username', FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, 'Password', FILTER_SANITIZE_STRING);
	$hashedPass = password_hash($password,PASSWORD_DEFAULT);
	if (empty($username) || empty($password)){
	?>
	<form method="post" class="albumForm">
		Login here:
		<br>
		<br>
		<label>Username</label>
		<br>
		<input type="text" name="Username">
		<br>
		<label>Password</label>
		<br>
		<input type="password" name="Password">
		<br>
		<input type="submit" name="submit" value="Login">
	</form>
	<?php
	} 
	else if ($username == "John Cena" && password_verify($password,$hashedPass)){
		print("You have logged in successfully, $username.");
		$_SESSION['logged_user'] = $username;
	} else {
		print("You did not login successfully. Please make sure your username and password are correct.");
		print("<p><a href=\"login.php\" class=\"photoDesc\">Click here to login</a></p>");
	} 
	?>
	<br><br><br><br><br><br><br>
	<form method="post" class="albumForm">
		Are you new? Create a user:
		<br>
		<br>
		<label>Username</label>
		<br>
		<input type="text" name="newUser">
		<br>
		<label>Password</label>
		<br>
		<input type="password" name="newPass">
		<br>
		<label>Retype Password</label>
		<br>
		<input type="password" name="newPassTwo">
		<br>
		<input type="submit" name="submitNew" value="Create User">;

		<?php
			$submitNew = isset($_POST["submitNew"])?$_POST["submitNew"]:"";
			if ($submitNew){
				$validated = true;
				$message = "";
				$newUser = htmlentities(isset($_POST["newUser"])?$_POST["newUser"]:"");
				$newPass = htmlentities(isset($_POST["newPass"])?$_POST["newPass"]:"");
				$newPassTwo = htmlentities(isset($_POST["newPassTwo"])?$_POST["newPassTwo"]:"");
				if ($newPass !== $newPassTwo){
					$validated = false;
					$message = "Please make sure your passwords match";
				}
				if (strlen($newUser)>20 || strlen($newPass)>20 || $newUser == "" || $newPass == ""){
					$validated = false;
					$message = "Your username and password must not be empty or longer than 20 characters";
				}
				if ($validated){
					$message = "User successfully added! You may now log in.";
					$hashedP = password_hash($newPass,PASSWORD_DEFAULT);
					$addQuery = $mysqli -> query("INSERT INTO users (username,hasspassword) VALUES ('$newUser','$hashedP')");
					if ($addQuery == false) print("Error in adding user");
				}
				print("<p>$message</p>");
			}
		?>
	</form>
	<div id="footer">
		<footer>Background image found at https://www.planwallpaper.com/static/images/Alien_Ink_2560X1600_Abstract_Background_1.jpg</footer><br>
	</div>
</body>

</html>

