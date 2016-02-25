<?php
//Makes a Pokemon from a $string read from data.txt. If $search is true, pokemon is styled as displayed on search page
function makePokemon($string,$search){
	$pokeInfo = explode("|",$string);
	$name = isset($pokeInfo[0])?$pokeInfo[0]:"";
	$types = isset($pokeInfo[1])?$pokeInfo[1]:"";
	$allAbilities = explode(",",isset($pokeInfo[2])?$pokeInfo[2]:"");
	$description = isset($pokeInfo[3])?$pokeInfo[3]:"";
	$imageURL = (isset($pokeInfo[4]) && (trim($pokeInfo[4] !== "")))?$pokeInfo[4]:"images/ditto.png";
	$bothTypes = explode(",",$types);
	if (trim($bothTypes[0]) === trim(isset($bothTypes[1])?$bothTypes[1]:"")){
		$types = trim($bothTypes[0]);
	}
	$firstAbility = $allAbilities[0];
	$secondAbility = isset($allAbilities[1])?$allAbilities[1]:"";
	$thirdAbility = isset($allAbilities[2])?$allAbilities[2]:"";
	$class = "";
	if ($search){
		$class = "searchMon";
	}
	else {
		$class = "pokemon";
	}

	return "<div class=$class>
			<p class=\"poke_name\">$name</p>
			<p class=\"poke_type\">$types</p>
			Abilities<ul class=\"poke_abilities\">
				<li>$firstAbility</li>
				<li>$secondAbility</li>
				<li>$thirdAbility</li>
			</ul>
			<img src=\"$imageURL\" alt='images/ditto.png' class=\"poke_image\">
			<p class=\"poke_description\">$description</p>
		</div>";
}

?>