<!-- Includes an array of all Pokemon types -->
<?php
$types = array(
"Choose",
"Normal",
"Fighting",
"Flying",
"Poison",
"Ground",
"Rock",
"Bug",
"Ghost",
"Steel",
"Fire",
"Water",
"Grass",
"Electric",
"Psychic",
"Ice",
"Dragon",
"Dark",
"Fairy"
);

function genPicklist($types){
	foreach ($types as $int => $type){
		echo "<option value=$type>$type</option>";
	}
}

?>
