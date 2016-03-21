<!DOCTYPE html>

<html class="other">

<head>
	<meta charset="UTF-8">
	<title>Photo Search</title>
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
	<?php
	require_once 'configLocal.php';
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
	<form method="post" class="albumForm">
		Search for a photo
		<br>
		<label>Photo Title (Required)</label>
		<br>
		<input type="text" name="PhotoTitle">
		<br>
		<br>
		<input type="submit" name="submit" value="Search">
	</form>

	<?php
		$submitted = isset($_POST["submit"])?true:false;
		if ($submitted){
			//Retrieves Photo Title from form, and ensures it is not empty or too long
			$photoTitle = trim(htmlentities(isset($_POST["PhotoTitle"])?$_POST["PhotoTitle"]:""));
			$validated  = true;
			if (strlen($photoTitle)>30 || $photoTitle == ""){
				$validated = false;
			}
			if ($validated){
				print("Search Results:");
				//Queries for all photos in the Photos table for which photoTitle is a substring of the Photo's title
				$imageSearchQuery = $mysqli -> query("SELECT * FROM Photos WHERE Photo_Title LIKE '%$photoTitle%'");
				if ($imageSearchQuery == false) print("Dayum");
				while ($row = $imageSearchQuery -> fetch_assoc()){
					//Displays photo with its title as a caption
					$FP = $row['File_Path'];
					$PT = $row['Photo_Title'];
					print("<figure>
						<img src=$FP>
						<figcaption>$PT</figcaption>
						</figure>");
				}
			}
			else {
				print("Please correct your invalid inputs (Photo Title cannot be empty or longer than 30 characters)");
			}	
		}
	?>
	<div id="footer">
		<footer>Background image found at https://www.planwallpaper.com/static/images/Alien_Ink_2560X1600_Abstract_Background_1.jpg</footer><br>
	</div>
</body>

</html>