<?php session_start(); ?>

<!DOCTYPE html>

<html class="other">

<head>
	<meta charset="UTF-8">
	<title>Album Home</title>
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
	<?php
	require_once 'configLocal.php';
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	if ($mysqli->errno){
		print('There was a mysqli error');
		print($mysqli->error);
		exit();
	}

	//Checks if aID in http URL query is a valid integer.
	//Integer casts return 0 upon failure
	$aID = isset($_GET['Album_ID'])?$_GET['Album_ID']:"";
	if ((int) $aID == 0 && $aID != ""){
		die("Error: Album_ID in URL must be an integer");
	}
	 ?>
</head>

<body class="homepage">
	<?php include 'albumHeader.php'; 
	if (isset($_SESSION['logged_user'])){
		$user = $_SESSION['logged_user'];
		print("<p id=\"loginTag\">You are logged in as $user</p>");
	}?>
	<div id="allPokePhotos">
		<?php
		if ($aID == ""){
			echo "<p>Choose an album to see more photos.</p>";
			$allAlbums = $mysqli -> query("SELECT * FROM Albums");
			while ($row = $allAlbums -> fetch_assoc()){
				$album = $row['Album_Name'];
				$cover = $row['Cover'];
				$aID = $row['Album_ID'];
				if (trim($cover) == ""){
					$cover = "./images/default.jpg";
				}
				$url = "AlbumHome.php?Album_ID=$aID";
				print("<div id=\"albumCover\">
						<a href=$url>
						<p class=\"photoDesc\">Album Name: $album</p>
						<img src=$cover class = \"pokePhoto\">
						</a>
						</div>");
			}
		}
		

		else {
			//Displays information about the user-selected album
			$albumQuery = $mysqli->query("SELECT * FROM Albums WHERE Album_ID = $aID");
			while ($row = $albumQuery -> fetch_assoc()){
				$aName = $row['Album_Name'];
				$dateCreated = $row['Date_Created'];
				$dateModified = $row['Date_Modified'];
				print("Album Name: $aName, Created on $dateCreated, Last modified on $dateModified");
				}

			//Resizes all images in album to have 400px width, 400px height
			//Displays all resized images
			$imageQuery = $mysqli -> query("SELECT * FROM Photos INNER JOIN Album_Images ON Photos.Photo_ID = Album_Images.Photo_ID WHERE Album_Images.Album_ID = $aID");
			print("<div id=\"allPokePhotos\">");
			while ($row = $imageQuery -> fetch_assoc()){
				$FP = $row['File_Path'];
				/*$imageSize = getimagesize($FP);
				switch ($imageSize['mime']) {
					case 'image/jpeg':
						$sourceFunc = 'imagecreatefromjpeg';
						$destFunc = 'imagejpeg';
						break;
					case 'image/jpg':
						$sourceFunc = 'imagecreatefromjpeg';
						$destFunc = 'imagejpeg';
					case 'image/png':
						$sourceFunc = 'imagecreatefrompng';
						$destFunc = 'imagepng';
						break;
					case 'image/gif':
						$sourceFunc = 'imagecreatefromgif';
						$destFunc = 'imagegif';
						break;
					default:
						throw new Exception("Invalid image");
				}
				$source = $sourceFunc($FP);
				$imageWidth = imagesx($source);
				$imageHeight = imagesy($source);
				$newImage = imagecreatetruecolor(400,400);
				imagecopyresized($newImage,$source,0,0,0,0,400,400,$imageWidth,$imageHeight);
				$destFunc($newImage,$FP);*/
				print("<img src=$FP style=\"width: 400px; height: 400px; \">");
			}
			print("</div>");
		}
		?>
	</div>
	<div id="footer">
		<footer>Background image found at https://www.planwallpaper.com/static/images/Alien_Ink_2560X1600_Abstract_Background_1.jpg</footer><br>
		<footer>Default album cover image found at https://i.ytimg.com/vi/NcLFrppIwYk/hqdefault.jpg</footer>
	</div>
</body>

</html>
