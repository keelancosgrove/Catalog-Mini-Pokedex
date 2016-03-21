<!--Obsolete Page-->



<!DOCTYPE html>

<html class="other">

<head>
	<meta charset="UTF-8">
	<title>Album Display</title>
	<link rel="stylesheet" type="text/css" href="../css/stylesheet.css">
	<?php
	require_once 'configLocal.php';

	//Connects to localhost database, returns error message on failures
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	if ($mysqli->errno){
		print('There was a mysqli error');
		print($mysqli->error);
		exit();
	}

	//Checks if aID in http URL query is a valid integer.
	//Integer casts return 0 upon failure
	$aID = isset($_GET['Album_ID'])?$_GET['Album_ID']:"";
	if ((int) $aID == 0 && $aId != ""){
		die("Error: Album_ID in URL must be an integer");
	}
	 ?>
</head>

<body id="homepage">
	<div id="imageNav">
		<h1>Albums</h1>
		<ul class="sections">
			<li><a href="../index.html" class="notCatalog">Home</a></li>
			<li><a href="../AlbumHome.php" class="notCatalog">Albums Front Page</a></li>
			<li><a href="../addAlbum.php" class="notCatalog">Add</a></li>
		</ul>
	</div>
	<?php //include 'albumHeader.php'; 

	//Displays information about the user-selected album
	$albumQuery = $mysqli->query("SELECT * FROM albums WHERE Album_ID = $aID");
	while ($row = $albumQuery -> fetch_assoc()){
		$aName = $row['Album_Name'];
		$dateCreated = $row['Date_Created'];
		$dateModified = $row['Date_Modified'];
		print("Album Name: $aName, Created on $dateCreated, Last modified on $dateModified");
	}
	?>

	<div id="allPokePhotos">
		<?php

			//Resizes all images in album to have 400px width, 400px height
			//Displays all resized images
			$imageQuery = $mysqli -> query("SELECT * FROM photos INNER JOIN album_images ON photos.Photo_ID = album_images.Photo_ID WHERE album_images.Album_ID = $aID");
			while ($row = $imageQuery -> fetch_assoc()){
				$FP = ".".$row['File_Path'];
				$otherFP = $row['File_Path'];
				$source = imagecreatefromjpeg($otherFP);
				$imageWidth = imagesx($source);
				$imageHeight = imagesy($source);
				$newImage = imagecreatetruecolor(400,400);
				imagecopyresized($newImage,$source,0,0,0,0,400,400,$imageWidth,$imageHeight);
				imagejpeg($newImage,$otherFP);
				print("<img src=$FP>");
			}
		?>
	</div>
	<div id="footer">
		<footer>Background image found at https://www.planwallpaper.com/static/images/colorful-triangles-background_yB0qTG6.jpg</footer><br>
	</div>
</body>

</html>