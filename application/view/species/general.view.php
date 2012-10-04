<?php


echo "<div id=\"fiche\">";

echo "<div id=\"taxobox\">";


//".species::GetSpeciesNameById($_GET['id_species'],$Language->Get())."
echo "<div class=\"title_box\">";


/*
  echo "<span>
  ".$data['info'][0][$GLOBALS['_LG']->Get()]."
  </span><br />";
 */
/*
  <img src=\"pictures/sexe/female.gif\" border=\"0\" align=\"top\" />
  <img src=\"pictures/sexe/male.gif\" border=\"0\" align=\"top\" />
 */
echo '<span class="right hidden" style="postion:absolute">[ <a href="dd">+</a> <a href="dd">/</a> <a href="dd">-</a> ]</span>'
 . str_replace("_", " ", $data['info'][0]['scientific_name']) . '</div>';




$alt = "";
$title = "";

if (false)
{

	$url = IMG . "crop/250x250/Eukaryota/{$ob3->Kingdom}/{$ob3->Phylum}/{$ob3->Class}/{$ob3->Order2}/{$ob3->Family}/{$ob3->Genus}/{$species}/{$ob3->Id}-{$species}.jpg";

	$alt = $ob->ScientificName;
	$title = $ob->ScientificName;
}
else
{
	$url = IMG . "main/nopictureavailable250.png";
}

echo '<div style="height:250px; overflow:hidden"><img src="' . $url . '" title="' . $title . '" alt="' . $alt . '" border="0" width="250" height="250" /></div>';


echo "<div class=\"title_box\">Biométrie</div>";

echo "<div class=\"biometrie\"><span>11 A??’A‚A  12 cm</span>Taille : </div>";
echo "<div class=\"biometrie\" style=\"background:#e5e5e5\"><span>15 A??’A‚A  16 cm</span>Envergure : </div>";
echo "<div class=\"biometrie\"><span>11 grammes</span>Poids : </div>";
echo "<div class=\"biometrie\" style=\"background:#e5e5e5\"><span>4 - 6</span>Nombre d'oeufs : </div>";
echo "<div class=\"biometrie\"><span>14 jours</span>Incubation : </div>";
echo "<div class=\"biometrie\" style=\"background:#e5e5e5\"><span>21 jours</span>Sortie du nid : </div>";
echo "<div class=\"biometrie\"><span>2.5</span>DiamA??’A‚A?tre de la bague : </div>";



echo '<div class="title_box">'.__("IUCN conservation status").'</div>';

if (empty($data['info'][0]['code_iucn']))
{
	$data['info'][0]['code_iucn'] = "NE";
}
foreach ($data['iucn'] as $iucn)
{
	
	if ($iucn['code_iucn'] == $data['info'][0]['code_iucn'])
	{
		$data['info'][0]['libelle'] = $iucn['libelle'];
		$background = $iucn['background'];
	}
	
}


echo "<div id=\"iucn\">";





echo '<div class="pourtour">';


foreach ($data['iucn'] as $iucn)
{
	$over = 'opacity:0.7;color:#fff;';
	$end = '';
	$arrow = '';
	
	if ($iucn['code_iucn'] == $data['info'][0]['code_iucn'])
	{
		$over = 'opacity:1; font-weight:700;color:#fff; border-bottom:#'.$iucn['background'].' 1px solid;';
		$arrow = '<img class="arrow" src="'.IMG.'10/arrow-up.gif" height="10" width="10" />';
		$end = 'margin-right :4px;margin-top:4px;';
		
		if ($iucn['code_iucn'] == 'NE')
		{
			$over .= 'border-left:#'.$iucn['background'].' 1px solid;';
		}
		else
		{
			$over .= 'border-left:#000 1px solid;';
		}
		
		if ($iucn['code_iucn'] == 'EX')
		{
			$over .= 'border-right:#'.$iucn['background'].' 1px solid;';
		}
		else
		{
			$over .= 'border-right:#000 1px solid;';
		}
	}
	else
	{
		$end .= 'margin-top:4px;';
		$over .= 'border-bottom:#000 1px solid;';
	}
	
	if ($iucn['code_iucn'] == "EX")
	{
		
		$end .= 'margin-right :0px;';
	}
	
	echo '<div class="left iucn" title="'.__($iucn['libelle']).'" style="background:#' . $iucn['background'] . '; ' . $over . '">'. $iucn['code_iucn'] . '</div>';
}
echo "<div class=\"clear\"></div>";

echo '<div class="libelle" style="background:#'.$background.'; color:#fff">' . __($data['info'][0]['libelle']) . "</div>";

echo '</div>';


echo "</div>";

//echo "<img src=\"pictures/main/244px-Status_iucn3.1_LC-fr.svg.png\" border=\"0\" />";

echo "<div class=\"title_box\">Distribution</div>";
echo "<img src=\"" . IMG . "main/repartition.png\" width=\"250\" height=\"169\" border=\"0\" />";





echo "</div>";


//echo "<h3 class=\"item\">" . __("Name") . "</h3>";

echo '<div>';
$lg = explode(",", LANGUAGE_AVAILABLE);

foreach ($data['info'][0] as $key => $vernacular_name)
{
	if (in_array($key, $lg))
	{
		if (!empty($vernacular_name))
		{
			echo '<div><img class="' . $key . '" src="' . IMG . '/main/1x1.png" width="18" height="12" border="0"> ' . $vernacular_name . '</div>';
		}
	}
}

echo '</div>';



echo "<h3 class=\"item\">" . __("Geographic range") . "</h3>";

if (count($data['geographic_range']) != 0)
{
	$name = "";
	
	foreach($data['geographic_range'] as $line)
	{
		if ($line['distribution'] != $name)
		{
			$name = $line['distribution'];
			echo "<br /><b>".$name."</b> : ";
		}
		
		echo '<img class="country" src="'.IMG.'/country/type2/'.  strtolower($line['iso']).'.png" width="16" height="11"> <a href="'.LINK.'species/country/'.strtolower($line['iso']).'/'.$data['info'][0]['family'].'/">'.$line['libelle']."</a>, ";
		
	}
}

//debug($data['geographic_range']);

echo "
		<h3 class=\"item\">" . __("Identification") . "</h3>
		<h3 class=\"item\">" . __("Habitat") . "</h3>
		<h3 class=\"item\">" . __("Behaviors") . "</h3>
		<h3 class=\"item\">" . __("Breeding") . "</h3>
		<h3 class=\"item\">" . __("Plan") . "</h3>";


if (count($data['source']) > 0)
{
	echo "<h3 class=\"item\">" . __("References") . "</h3>";

	foreach ($data['source'] as $ref)
	{
		echo '<div><img src="' . IMG . '16/' . $ref['pic16'] . '"> <a href="' . $ref['reference_url'] . '" class="external" target="_BLANK">' . $ref['name'] . ' : ' . $ref['reference_id'] . '</a> 
			(' . __('Added') . ' : ' . $ref['date_created'] . ' - ' . __('Last update') . ' : ' . $ref['date_updated'] . ')</div>';
	}
}




echo "</div>";