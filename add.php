<!DOCTYPE html>

<html class="catalog">

<head>
	<meta charset="UTF-8">
	<title>Add a Pokemon </title>
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
</head>

<body>
	<?php include 'types.php'; ?>
	<div id="wrapper">
		<?php include 'displayHeader.php'; ?>

		<div id="content">
			<!--Creates form to update data.txt, thus adding a Pokemon to index.php -->
			<form action="add.php">
				Add a Pokemon to the catalog:
				<br>
				<br>
				<label>Name (Required)</label>
				<br>
				<input type="text" name="Name">
				<br>
				<label>Type(s) (Required: If only one type, choose it in both picklists)</label>
				<br>
				<select name="type1">
					<?php genPicklist($types); ?>
				</select>
				<select name="type2">
					<?php genPicklist($types); ?>
				</select>
				<br>
				<div id="abilityAdd">
					<label>Ability 1 (Required)</label>
					<input type="text" name="ability1">
					<label>Ability 2 (Optional)</label>
					<input type="text" name="ability2" class="optionalAbility">
					<label>Ability 3 (Optional)</label>
					<input type="text" name="ability3" class="optionalAbility">
				</div>
				<br>
				<label>Description (Required)</label>
				<br>
				<textarea rows="8" cols="30" name="description"></textarea>
				<br>
				<label>Image Link (optional)</label>
				<br>
				<input type="text" name="image">
				<br>
				<input type="submit" name="submit" value="Add the Pokemon">
				
				<?php
				$delimiter = '|';
				$submitted = isset($_GET["submit"])?true:false;
				$validated = true;
				$message = "";
				if ($submitted){
					//Opens data.txt and retrieves input fields from form
					$file = fopen("data.txt","a");
					if (!$file){
						die("There was a problem opening data.txt");
					}
					$name = isset($_GET["Name"])?$_GET["Name"]:"";
					$type1 = isset($_GET["type1"])?$_GET["type1"]:"";
					$type2 = isset($_GET["type2"])?$_GET["type2"]:"";
					$ability1 = isset($_GET["ability1"])?$_GET["ability1"]:"";
					$ability2 = isset($_GET["ability2"])?$_GET["ability2"]:"";
					$ability3 = isset($_GET["ability3"])?$_GET["ability3"]:"";
					$description = isset($_GET["description"])?filter_input(INPUT_GET,"description",FILTER_SANITIZE_FULL_SPECIAL_CHARS):"";
					$image = isset($_GET["image"])?$_GET["image"]:"";
					//Checks if the name is made up of only letters, or is some variant of Porygon2
					if (!preg_match("/^[A-Za-z ]*$/",$name) && ($name !=="Porygon2") && ($name !=="Porygon 2") && ($name !== "porygon 2") && ($name !== "porygon2")){
						$validated = false;
						$message = "You must enter a valid name";
					}
					//Checks if the name has at least three characters
					if (strlen($name) < 3 || strlen($name)>25){
						$validated = false;
						$message = "Name must have between 3 and 25 characters";
					}
					//Checks if a Pokemon with the same name already exists, which are considered as duplicates
					$lines = file("data.txt");
					foreach ($lines as $string){
						$pokemon = explode("|",$string)[0];
						if (trim($name) === trim($pokemon)){
							$validated = false;
							$message = "That pokemon already exists in this catalog";
						}
					}
					//Checks if the user has selected two types 
					if (($type1 === "Choose") || ($type2 === "Choose")){
						$validated = false;
						$message = "You must choose two types (for a Pokemon with only one type, select it in both picklists)";
					}
					//Checks if ability 1 has at least three characters and consists of only letters
					if ((strlen($ability1)<3) || !preg_match("/^[A-Za-z]*/",$ability1)){
						$validated = false;
						$message = "You must enter a value for ability 1 with at least 3 characters";
					}
					//Checks if the description has at least 20 characters
					if (strlen($description)<20){
						$validated = false;
						$message = "The description must have at least 20 characters.";
					}
					//Checks if either the URL is empty or is a valid URL according to PHP's URL validator
					if ((trim($image) !== "") && !filter_input(INPUT_GET,"image",FILTER_VALIDATE_URL)){
						$validated = false;
						$message = "The image URL must be either blank or a valid URL";
					}
					//If none of the validation tests fail, writes the inputted Pokemon to data.txt which updates index.php
					if ($validated){
						fwrite($file,"\n$name|$type1, $type2|$ability1,$ability2,$ability3|$description|$image|");
						$message = "Pokemon added!";
					}
					fclose($file);
				}
				echo "$message";
				?>
				</form>
		</div>


		<div id="footer">
			<footer>Background image found at http://www.quicktoptens.com/wp-content/uploads/2014/07/1367040470012.jpg
				Pokemon images found at Bulbapedia</footer>
		</div>
	</div>
</body>

</html>