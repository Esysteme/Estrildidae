<?php

use \glial\species\Species;

echo "<ul id=\"\" class=\"menu_tab\" style=\"padding-left: 3px;\">";

foreach ( $data['pending'] as $tab )
{
	echo '<li id="' . $tab['name'] . '">';
	echo '<a href="' . $tab['name'] . '" style="padding-left: 24px; background: #eee url(' . IMG . '16/' . $tab['pic16'] . ') 5% 0% no-repeat">' . $tab['name'] . ' (' . $tab['cpt'] . ')</a>';
	echo '</li>';
}


echo "</ul>";

/*
  $sub = $data['param'];
  $rr = new controller("species", $method, json_encode($sub));
  $rr->recursive = true;
  $rr->get_controller();

 */




$gg = Species::get($data['param'][0]);


$author = -1;
$tag_search = -1;



foreach ( $gg as $tab )
{
	if ( $author != $tab['id_species_author'] )
	{
		if ( $author !== -1)
		{
			echo '</form>';
		}
		echo '<form class="pending" name="" action="" method="post">';
		echo '<h3>' . $tab['surname'] . ' <a href="">Accept this author</a> | <a href="">Remove this author</a> | <a href="'.LINK.'author/image/'.$tab['id'].'">View all</a></h3>';
		$author = $tab['id_species_author'];
		$new = 1;
	}

	if ( $tag_search != $tab['tag_search'] || $new == 1 )
	{
		echo '<h3 style="color:red">' . $tab['tag_search'] . '</h3>';
		$tag_search = $tab['tag_search'];
	}


	
	if ($tab['gg'] > 1)
	{
		$class=  "img_valid";
	}
	else
	{
		$class =  "img_dunno";
	}
	
	echo '<span class="img_">';
	echo '<img class="selector '.$class.'" src="' . str_replace("_s","_q",$tab['miniature']) . '" /> ';
	echo '<input type="hidden" name="link__species_picture_id__species_picture_search['.$tab['id_link'].']" value="1" />';
	echo '<span><img src="' . str_replace("_s","_z",$tab['miniature']) . '" /></span>';
	echo '</span>';

	$new++;
}
