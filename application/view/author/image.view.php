<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo "<div id=\"menu_admin_crop\">";
echo "<div class=\"title_box\"><a href=\"\">" . __('Photo') . "</a></div>";
echo "<div>";
echo '<img src="' . IMG . 'user/acspike_male_user_icon.png" alt="" />';
echo "</div>";




echo "<div class = \"title_box\"><a href=\"\">" . __('My friends') . "</a></div>";
echo "<div class = \"title_box\"><a href=\"\">" . __('Visitors') . "</a></div>";
echo "</div>";



include_once(LIBRARY."Glial/species/species.php");



echo '<div style="margin-left:260px;">';

echo '<h3>'.__("Photo valided").'</h3>';
echo '<div id="photo">';
foreach ($data['photo'] as $var)
{
	

	$species_name = str_replace(" ", "_", $var['nominal']);
	$path = "Eukaryota/{$var['kingdom']}/{$var['phylum']}/{$var['class']}/{$var['order']}/{$var['family']}/{$var['genus']}/" . $species_name;
	$picture_name = $var['id_photo'] . "-" . $species_name . ".jpg";
	$img = FARM1 . "crop/" . SIZE_MINIATURE_SMALL . "x" . SIZE_MINIATURE_SMALL . "/" . $path . DS . $picture_name;
	
	
	$url = LINK."species/nominal/".$species_name."/photo/photo_detail/".$var['id_photo'];
	
	
	species_tree::html_pic($url, $img, $var['nominal'], $var['nominal']."\n(".$var['info_photo'].")");
	
}

echo "</div>";


echo '<div class="clear"></div>';
echo '<h3>'.__("Photo pending").'</h3>';


//debug($data['to_valid']);


echo '<div id="photo">';
foreach ($data['to_valid'] as $var)
{
	
	echo '<img src="'.$var['miniature'].'" width="'.$var['width'].'" height="'.$var['height'].'" />';
	
	/*
	$species_name = str_replace(" ", "_", $var['nominal']);
	$path = "Eukaryota/{$var['kingdom']}/{$var['phylum']}/{$var['class']}/{$var['order']}/{$var['family']}/{$var['genus']}/" . $species_name;
	$picture_name = $var['id_photo'] . "-" . $species_name . ".jpg";
	$img = $var['miniature'];
	
	
	$url = "";

	
	species_tree::html_pic($url, $img, $var['nominal'], $var['nominal']);
	*/
}

echo "</div>";


echo '<div class="clear"></div>';








/////////////////


echo '<h3>'.__("Photo denied").'</h3>';


//debug($data['to_valid']);


echo '<div id="photo">';
foreach ($data['removed'] as $var)
{
	
	echo '<img src="'.$var['miniature'].'" width="'.$var['width'].'" height="'.$var['height'].'" />';
	
	/*
	$species_name = str_replace(" ", "_", $var['nominal']);
	$path = "Eukaryota/{$var['kingdom']}/{$var['phylum']}/{$var['class']}/{$var['order']}/{$var['family']}/{$var['genus']}/" . $species_name;
	$picture_name = $var['id_photo'] . "-" . $species_name . ".jpg";
	$img = $var['miniature'];
	
	
	$url = "";

	
	species_tree::html_pic($url, $img, $var['nominal'], $var['nominal']);
	*/
}

echo "</div>";
echo '<div class="clear"></div>';



echo "</div>";