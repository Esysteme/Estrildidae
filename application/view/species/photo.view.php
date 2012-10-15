<?php

include_once(LIBRARY."Glial/species/species.php");


echo '<div id="photo">';
foreach ($data['photo'] as $var)
{
	

	$species_name = str_replace(" ", "_", $var['nominal']);
	$path = "Eukaryota/{$var['kingdom']}/{$var['phylum']}/{$var['class']}/{$var['order']}/{$var['family']}/{$var['genus']}/" . $species_name;
	$picture_name = $var['id_photo'] . "-" . $species_name . ".jpg";
	$img = FARM1 . "crop/" . SIZE_MINIATURE_SMALL . "x" . SIZE_MINIATURE_SMALL . "/" . $path . DS . $picture_name;
	$url ="#".LINK."species/nominal/".$species_name."/photo_detail/".$var['id_photo']."/photo_detail>content_1";
	
	
	
	species_tree::html_pic($url, $img, $var['info_photo'], $species_name);
	
	
	
}

echo "</div>";