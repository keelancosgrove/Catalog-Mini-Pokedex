<!DOCTYPE html>
<!--Bugs: After adding album, all checkboxes dissapear. Photos not being resized to 400px with optimal image clarity-->
<html class="other">

<head>
	<meta charset="UTF-8">
	<title>Image Database</title>
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
	<?php
	//Creates mysqli object required for querying, using the DB specs in the provided config file
	require_once 'configLocal.php';
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	if ($mysqli->errno){
		print('mysqli error happened');
		print($mysqli->error);
		exit();
	}
	$albumNameQuery = $mysqli -> query("SELECT Album_Name, Album_ID FROM Albums");
	$photoNameQuery = $mysqli -> query("SELECT Photo_Title, Photo_ID FROM Photos");
	$checkboxes = "";
	while ($row = $albumNameQuery -> fetch_assoc()){
		$aName = $row['Album_Name'];
		$aID = $row['Album_ID'];
		$checkboxes = $checkboxes."<input type=\"checkbox\" name=\"alb[]\" value=$aID>$aName";
	}
	 ?>
</head>

<body class="homepage">
	<?php include 'albumHeader.php'; ?>
	<!--TODO: use global variable for checkboxes, update it when adding an album-->
	<!--Form for album uploading-->
	<div id="albumForm">
		<form action="addAlbum.php" method = "post" class="albumForm">
			<p>Use this form to add an album</p>
			<br>
			<br>
			<label>Album Name (Required)</label>
			<br>
			<input type="text" name="albumName">
			<br>
			<label>Cover Photo (optional)</label>
			<br>
			<input type="text" name="coverPhoto">
			<br>
			<input type="submit" name="submitAlbum" value="Add the Album">
		</form>

		<?php
			$validated = true;
			$submitted = isset($_POST["submitAlbum"])?true:false;
			if($submitted){

				//Retrieves user-submitted album name and cover photo URL
				$albumName = isset($_POST["albumName"])?$_POST["albumName"]:"";
				$coverPhoto = isset($_POST["coverPhoto"])?$_POST["coverPhoto"]:"";
				$albumName = htmlentities($albumName);

				//Checks if user inputs are valid, otherwise sets flag as false
				if (strlen($albumName) > 30 || trim($albumName) == ""){
					$validated = false;
				}
				if (trim($coverPhoto) != "" && !filter_input(INPUT_POST,"coverPhoto",FILTER_VALIDATE_URL)){
					$validated = false;
				}
				//Ensures that the user cannot add an album if an album with the same name already exists
				while ($row = $albumNameQuery -> fetch_assoc()){
					if ($row['Album_Name'] == $albumName){
						$validated = false;
					}
				}
				$dateCurrent = date("Y-m-d");
				//Checks if all user inputs are valid before querying
				if ($validated){
					$query = "INSERT INTO Albums (Album_Name, Date_Created, Date_Modified, Cover) VALUES ('$albumName','$dateCurrent','$dateCurrent','$coverPhoto')";
					if ($query == false) print("Not again");
					if ($mysqli -> query($query) == true){
						print("Album successfully added!");
						$newestAlbumID = $mysqli -> insert_id;
						/*$getMaxAID = $mysqli -> query("SELECT MAX(Album_ID) FROM Albums LIMIT 1");
						$row = $getMaxAID -> fetch_row();
						$max = $row[0];*/
						$checkboxes = $checkboxes."<input type=\"checkbox\" name=\"alb[]\" value=$newestAlbumID>$albumName";
					}
					else print("Did not update successfully");
					//$mysqli -> close();
				}
				else {
					print("Please correct your invalid inputs (Album Name cannot be empty or longer than 30 characters)");
				}
			}
		?>
	</div>
	<br><br><br><br><br><br><br><br>

	<!--Form for photo uploading-->
	<div id = "photoForm">
		<!--<?php print_r($_FILES); ?>-->
		<!--<?php print_r($_POST); ?>-->
		<form action="" method="post" enctype="multipart/form-data" class="albumForm">
			Use this form to add a photo to one or more albums.
			<br>
			<br>
			<label>Photo Title (Required)</label>
			<br>
			<input type="text" name="photoTitle">
			<br>
			<br>
			<label>Album (Select all albums you wish to add the photo to)</label>
			<br>
			<?php
				print($checkboxes);
			?>
			<br>
			<br>
			<label>Photo (single upload)</label>
			<br>
			<input type="file" name="uploadedPhoto" accept="image/*">
			<br>
			<label>If you took the image from an outside source, please include citation here (leave blank if it's your image)</label>
			<br>
			<input type="text" name="citation">
			<br>
			<br>
			<input type="submit" name="submitPhoto" value="Add Photo">

			<?php
				$validPhoto = true;
				$submittedP = isset($_POST["submitPhoto"])?true:false;
				if ($submittedP){
					
					$photoTitle = isset($_POST["photoTitle"])?$_POST["photoTitle"]:"";
					$photo = $_FILES["uploadedPhoto"];
					$photoName = $photo["name"];
					$tempName = $photo["tmp_name"];
					$citation = isset($_POST["citation"])?$_POST["citation"]:"User submitted";
					if (trim($photoTitle) == "" || strlen($photoTitle)>30){
						$validPhoto = false;
					}
					$imagesize = getimagesize($tempName);
					if ($imagesize == false){
						$validPhoto = false;
					}
					while ($row = $photoNameQuery -> fetch_assoc()){
						$photoT = $row["Photo_Title"];
						if ($photoT == $photoTitle){
							$validated = false;
						}
					}

					//Validate that the uploaded file is an image
					if ($validPhoto){
						move_uploaded_file($tempName, "images/$photoName");
						print("Photo successfully uploaded");

						//Inserts photo into photos table
						$photoQuery = $mysqli -> query("INSERT INTO Photos (Photo_Title, File_Path, File_Credit) VALUES ('$photoTitle','images/$photoName','$citation')"); 
						if ($photoQuery == false) print("Photo upload error");
						$newestPhotoID = $mysqli -> insert_id;

						//For each checked checkbox, inserts the photo into the appropriate album
						$checkboxl = isset($_POST["alb"])?$_POST["alb"]:"";
						if ($checkboxl != ""){
							for ($i=0; $i<sizeof($checkboxl); $i++){
								$iAlbum = $checkboxl[$i];
								$mysqli -> query("INSERT INTO Album_Images (Album_ID, Photo_ID) VALUES ('$iAlbum','$newestPhotoID')");
							}
						}

					}
					else {
						print("Please correct your invalid inputs (Photo Title cannot be empty or longer than 30 characters)");
					}
				}
			?>
		</form>
	</div>

	<div id="footer">
		<footer>Background image found at https://www.planwallpaper.com/static/images/Alien_Ink_2560X1600_Abstract_Background_1.jpg</footer>
	</div>
</body>

</html>