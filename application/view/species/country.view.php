<?php

foreach ($data['species'] as $var)
{
	echo "<span class=\"shadowImage\">";
	echo '<div class="photo_link">';
	echo "<a href=\"" . LINK . "\">";

	echo '<div class="bigleaderpix">
<div class="caption">
<p>' . $var['scientific_name'] . '</p>
</div>
<div class="bigleaderlien"></div>
<img width="158" height="158" alt="' . $var['scientific_name'] . '" title="' . $var['scientific_name'] . '" src="' . IMG . 'main/nopictureavailable.png">
</div>';

	echo '</a></div></span>';
}

foreach ($data['country'] as $line)
{

	echo '<img class="country" src="' . IMG . '/country/type2/' . strtolower($line['iso']) 
		. '.png" width="16" height="11"> <a href="' . LINK . 'species/country/' . strtolower($line['iso']) . '/' .'Estrildidae'
		. '/">' 
		. $line['libelle'] 
		. ' (' 
		. $line['cpt'] .')</a><br />';
	}	