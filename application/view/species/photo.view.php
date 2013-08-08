<?php

//include_once(LIBRARY . "glial/species/species.php");
use \glial\species\Species;

$scientific_name = "";



echo '<div id="photo">';
foreach ($data['photo'] as $var)
{

	if (!empty($var['scientific_name']))
	{
		if ($scientific_name != $var['scientific_name'])
		{
			echo '<div class="clear"></div>';
			echo '<h3>' . $var['scientific_name'] . '</h3>';
			$scientific_name = $var['scientific_name'];
		}
	}

	$species_name = str_replace(" ", "_", $var['nominal']);
	$path = "Eukaryota/{$var['kingdom']}/{$var['phylum']}/{$var['class']}/{$var['order']}/{$var['family']}/{$var['genus']}/" . $species_name;
	$picture_name = $var['id_photo'] . "-" . $species_name . ".jpg";
	$img = FARM1 . "crop/" . SIZE_MINIATURE_SMALL . "x" . SIZE_MINIATURE_SMALL . "/" . $path . DS . $picture_name;
	$url = LINK . "species/nominal/" . $species_name . "/photo/photo_detail/" . $var['id_photo'] . "/";



	Species::html_pic($url, $img, $var['info_photo'], $species_name);
}

echo "</div>";


$gg = Species::get($data['photo'][0]['id_nominal']);


$author = -1;
$tag_search = -1;
foreach ($gg as $tab)
{
	if ($author != $tab['id_species_author'])
	{
		echo '<h3>'.$tab['id_species_author'].'</h3>';
		$author = $tab['id_species_author'];
	}
	
	if ($tag_search != $tab['tag_search'])
	{
		echo '<h3 style="color:red">'.$tab['tag_search'].'</h3>';
		$tag_search = $tab['tag_search']; 
	}
	
	if ($tab['gg'] > 1 )
	{
		
		$border = "border-left:#00f 3px solid";
	}
	else
	{
		$border = "background:#fff; padding:2px";
		
	}
	echo $tab['gg']. ' <img src="'.$tab['miniature'].'" style="'.$border.'" height="" width="" /> ';
}