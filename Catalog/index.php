<!DOCTYPE html>

<html>

<head>
	<meta charset="UTF-8">
	<title>Water-Type Pokemon</title>
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
	<!--Displays a warning message to be used when user tries to reset form. 
	Code ideas taken from http://www.techtut.com/Tutorial/JavaScript/91-Creating-warning-messages-in-submit-forms.html -->
	<script type = "text/javascript">
		function warningMessage(){
			var decide = confirm("Are you sure that you want to reset the text file? You will not be able to recover it once reset.");
			if (decide){
				return true;
			}
			else {
				return false;
			}
		}
	</script>
</head>

<body>
	<?php include 'types.php';
	include 'makePokemon.php'; ?>
	<div id="wrapper">
		<?php include 'displayHeader.php'; ?>

		<div id="content">
			<form action="index.php">
			<!--Clears data.txt and returns the user to the refreshed index.php page
			Also displays warning message before resetting -->
			<p>Click here to reset the data file and clear all entries on this page (including the Pokemon that were here by default)</p>
			<input type="submit" name="submitted" id = "resetter" value="Reset" onClick="return warningMessage();">
			<?php
				if (isset($_GET["submitted"])){
					$file = fopen("data.txt","a");
					ftruncate($file,0);
					fclose($file);
					header('Location: index.php');
				}
			?>

			</form>
			<!--Reads lines from data.txt and creates Pokemon divs from each one
			makePokemon creates a div representing Pokemon information using the data structure in data.txt -->
			<?php
			$file = fopen("data.txt","a");
			if(!$file){
				die("There was a problem opening data.txt");
			}
			$lines = file("data.txt");
			foreach ($lines as $string){
				$pokemon = explode("|",$string);
				if (!empty(trim($pokemon[0]))){
					echo(makePokemon($string,false));
				}
			}
			?>
			
		</div>

		
		<div id="footer">
			<footer>Background image found at http://www.quicktoptens.com/wp-content/uploads/2014/07/1367040470012.jpg
				Pokemon images found at Bulbapedia</footer>
		</div>
	</div>
</body>

</html>