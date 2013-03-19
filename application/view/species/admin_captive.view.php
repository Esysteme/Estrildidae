<?php



echo '<form action="" method="POST">';



echo '<table class="table" id="variante">
<tbody><tr>
<th class="datagrid-cell" colspan="5">' . __("Species detained") . '</th>
<th class="datagrid-cell" colspan="4">' . __("For sale") . '</th>
<th class="datagrid-cell" colspan="3">' . __("Exchange") . '</th>
<th rowspan="2">' . __("Action") . '</th>
</tr>


<th class="datagrid-cell">' . __("Scientific name") . '</th>
<th>' . __("Subspecies") . '</th>
<th>' . __("Male") . '</th>
<th>' . __("Female") . '</th>
<th>' . __("Unknow") . '</th>

<th>' . __("Male") . '</th>
<th>' . __("Female") . '</th>
<th>' . __("Unknow") . '</th>
<th>' . __("Price per unit") . '</th>
	

<th>' . __("Male") . '</th>
<th>' . __("Female") . '</th>
<th>' . __("Unknow") . '</th>


</tr>';

if ( !empty($_GET['filter']['nbrows']) )
{
	$nbrows = $_GET['filter']['nbrows'];
}
else
{
	$nbrows = 1;
}

for ( $i = 1; $i <= $nbrows; $i++ )
{
	$disable = '';
	if ( $nbrows == 1 )
	{
		$disable = 'disabled="disabled"';
	}
	
	
	echo '<tr id="tr-' . ($i) . '" class="blah">

	<td align="center">';
	echo autocomplete("link__species_sub__user_main", "id_species_main", "textform species",$i);

	echo '</td><td>';
	echo select("link__species_sub__user_main", "id_species_sub", array(),"","textform subspecies",0,$i);

	
	echo '</td><td>';
	
	empty($_GET["link__species_sub__user_main"][$i]["male"])? $_GET["link__species_sub__user_main"][$i]["male"] = "0":"";
	empty($_GET["link__species_sub__user_main"][$i]["female"])? $_GET["link__species_sub__user_main"][$i]["female"] = "0":"";
	empty($_GET["link__species_sub__user_main"][$i]["unknow"])? $_GET["link__species_sub__user_main"][$i]["unknow"] = "0":"";

	echo input("link__species_sub__user_main", "male", "textform input-number male only_integer_positif", $i);
	echo '</td><td>';
	echo input("link__species_sub__user_main", "female", "textform input-number female only_integer_positif", $i);
	echo '</td><td>';
	echo input("link__species_sub__user_main", "unknow", "textform input-number unknow only_integer_positif", $i);
	
	// a vendre
	echo '</td><td>';
	echo select("link__species_sub__user_main__for_sale", "forsale_male", array("0"),"0","textform forsale_male int",0,$i);
	echo '</td><td>';
	echo select("link__species_sub__user_main__for_sale", "forsale_female", array("0"),"0","textform forsale_female int",0,$i);
	echo '</td><td>';
	echo select("link__species_sub__user_main__for_sale", "forsale_unknow", array("0"),"0","textform forsale_unknow int",0,$i);

	echo '</td><td>';
	echo input("link__species_sub__user_main__for_sale", "price", "textform price only_integer_positif", $i);
	echo ' €';
	
	//echo select("link__species_sub__user_main", "devise", array('€','$','£'),"0","textform devise int",0,$i);
	//echange
	echo '</td><td>';
	echo select("link__species_sub__user_main__exchange", "exchange_male", array("0"),"0","textform exchange_male int",0,$i);
	echo '</td><td>';
	echo select("link__species_sub__user_main__exchange", "exchange_female", array("0"),"0","textform exchange_female int",0,$i);
	echo '</td><td>';
	echo select("link__species_sub__user_main__exchange", "exchange_unknow", array("0"),"0","textform exchange_unknow int",0,$i);
	
	
	
	echo '</td>
	<td>
	<input id="delete-' . ($i) . '" class="delete-line button btGrey overlayW btMedium" type="button" value="Effacer" style="margin:0;" ' . $disable . ' />
	</td>
	</tr>';
}

echo '</tbody></table>';

echo '<br />';
echo '<input type="checkbox" name="all_for_sale" /> <b>'.__('If checked, all your species are for sale.').'</b><br />';
echo '<input type="checkbox" name="all_for_sale" /> <b>'.__('Display quantities of my species to the members.').'</b><br />';	
echo "<br />";
echo '<input id="add" type="button" class="button btBlueTest overlayW btMedium" value="' . __('Add a species') . '" />';
echo ' - ';
echo '<input id="add" type="submit" class="button btBlueTest overlayW btMedium" value="' . __('Save') . '" />';
echo '</form>';