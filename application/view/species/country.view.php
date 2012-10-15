<?php

include_once(LIBRARY . "Glial/species/species.php");


$libelle = "";


foreach ($data['species'] as $var)
{

	if ($var['libelle'] != $libelle)
	{
		if ($libelle != "")
		{
			echo '<div class="clear"></div>';
		}
		
		
		echo "<h3>".$var['libelle']."</h3>";
		
		$libelle = $var['libelle'];
		
	}

	$url = LINK . '/species/nominal/' . str_replace(" ", "_", $var['scientific_name']) . '/';
	$img = IMG . 'main/nopictureavailable.png';

	species_tree::html_pic($url, $img, $var['scientific_name'], $var['scientific_name']);
}

