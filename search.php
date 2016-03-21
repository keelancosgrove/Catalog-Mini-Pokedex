<!DOCTYPE html>
<html class="catalog">

<head>
	<meta charset="UTF-8">
	<title>Search</title>
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
</head>

<body>
	<?php include 'types.php';
	include 'makePokemon.php'; ?>
	<div id="wrapper">
		<?php include 'displayHeader.php'; ?>

		<div id="content">
			<form>
				Search for a Pokemon in the catalog (must use at least one field)
				<br>
				<br>
				<label>Name</label>
				<br>
				<input type="text" name="Name">
				<br>
				<label>Type (If only one type, choose it in both picklists)</label>
				<br>
				<select name="type1">
					<?php genPicklist($types); ?>
				</select>
				<select name="type2">
					<?php genPicklist($types); ?>
				</select>
				<br>
				<div id="abilitySearch">
					<label>Ability 1</label>
					<input type="text" name="ability1">
					<label>Ability 2</label>
					<input type="text" name="ability2">
					<label>Ability 3</label>
					<input type="text" name="ability3">
				</div>
				<br>
				<input type="submit" name="submit" value="Search">
				<?php
				if (isset($_GET["submit"])){
					//Wrapper function for strpos if needle is not empty string, otherwise defaults to true
					//strpos outputs a warning on empty string, otherwise checks if $needle is a substring of $haystack
					function betterstrpos($haystack,$needle){
						if ($needle === ""){
							return true;
						}
						else {
							return strpos($haystack,$needle);
						}
					}
					//$count represents the number of pokemon in data.txt that matched the user's search query
					//$validated is true if there are no validation errors in user input, false otherwise
					$count = 0;
					$validated = true;
					$validationMessage ="";
					$name = trim(strtolower(isset($_GET["Name"])?$_GET["Name"]:""));
					$type1 = isset($_GET["type1"])?$_GET["type1"]:"";
					$type2 = isset($_GET["type2"])?$_GET["type2"]:"";
					$ability1 = trim(strtolower(isset($_GET["ability1"])?$_GET["ability1"]:""));
					$ability2 = trim(strtolower(isset($_GET["ability2"])?$_GET["ability2"]:""));
					$ability3 = trim(strtolower(isset($_GET["ability3"])?$_GET["ability3"]:""));
					$lines = file("data.txt");
					$foundPokemon = array();
					//Input validation for name and ability. Since types are from a picklist, they do not need to be validated.
					//Checks if name is made of only letters and spaces, unless it's a variation of Porygon 2. 
					//Ho-Oh has a hyphen in his name, but he's a stupid Pokemon anyways
					//Also ensures length of name isn't too long
					if (!preg_match("/^[A-Za-z ]*$/",$name) && ($name !=="Porygon2") && ($name !=="Porygon 2") && ($name !== "porygon 2") && ($name !== "porygon2")){
						$validated = false;
						$validationMessage = "Your name search query must consist of only letters or some variation of Porygon2";
					}
					if (strlen($name)>25){
						$validated = false;
						$validationMessage = "Your name search query must consist of less than 25 characters.";
					}
					//Checks if abilities are only letters/spaces and are not longer than 35 characters
					if (!preg_match("/^[A-Za-z ]*$/",$ability1) || !preg_match("/^[A-Za-z ]*$/",$ability2) || !preg_match("/^[A-Za-z ]*$/",$ability2)){
						$validated = false;
						$validationMessage = "Your ability search queries must consist of only letters.";
					}
					if (strlen($ability1) > 35 || strlen($ability2) > 35 || strlen($ability3)>35){
						$validated = false;
						$validationMessage = "Your ability search queries must consist of less than 35 characters.";
					}
					foreach ($lines as $string){
						//found is true if the input has been found in data.txt, false otherwise
						//search is an "AND" based search, so all three found variables must be true to output a result
						$foundName = false;
						$foundType = false;
						$foundAbility = false;
						$pokeInfo = explode("|",$string);
						$pokeName = trim(strtolower(isset($pokeInfo[0])?$pokeInfo[0]:""));
						$types = isset($pokeInfo[1])?$pokeInfo[1]:"";
						$allAbilities = explode(",",isset($pokeInfo[2])?$pokeInfo[2]:"");
						$bothTypes = explode(",",$types);
						$pokeType1 = trim(isset($bothTypes[0])?$bothTypes[0]:"");
						$pokeType2 = trim(isset($bothTypes[1])?$bothTypes[1]:"");
						//Checks if the input search name is a substring of any pokemon's name in data.txt
						if (betterstrpos($pokeName,$name) !== false){
							$foundName = true;
						}
						//Checks if either input search types match a type of a pokemon in data.txt
						if (($type1 === "Choose") && ($type1 === "Choose")){
							$foundType = true;
						}
						else if (($type1 === "Choose" || $type2 === "Choose") && 
							($type1 === $pokeType1 || $type1 === $pokeType2 || $type2 === $pokeType1 || $type2 === $pokeType2)){
							$foundType = true;
						}
						else if (($type1 === $pokeType1 && $type2 === $pokeType2) || ($type1 === $pokeType2) && ($type2 === $pokeType1)){
							$foundType = true;
						}
						//Checks if any input search abilities match an ability of a pokemon in data.txt
						foreach ($allAbilities as $abilityUntrimmed){
							$ability = trim(strtolower($abilityUntrimmed));
							if (($ability1 === $ability) || ($ability2 === $ability) || ($ability3 === $ability) ||
							 ($ability1 === "" && $ability2 === "" && $ability3 === "")){
								$foundAbility = true;
							}
						}
						if ($foundAbility && $foundName && $foundType) {
							$count = $count + 1;
							$foundPokemon[] = $string;
						}
					}
					//Checks to see if at least one input field has been filled out, otherwise returns error message
					//Also checks if all input fields have been validated, and displays the number of search results 
					if ($name === "" && $type1 === "Choose" && $type2 === "Choose" && $ability1 === "" && $ability2 === "" && $ability3 === ""){
						print '<p id="searchResult">You must use at least one field to search</p>';
					}
					else if (!$validated){
						echo "<p id=\"searchResult\">$validationMessage</p>";
					}
					else if ($count === 0){
						echo '<p id = "searchResult">Sorry, no results matching your search query were found </p>';
					}
					else {
						echo "<p id =\"searchResult\">Your search results are displayed below: $count Pokemon found </p>";
						foreach ($foundPokemon as $pokemon){
							echo(makePokemon($pokemon,true));
						}
					}
				}
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