<?php$id_node = "node-".uniqid();echo "<div id=\"species_detail\">";echo "<ul id=\"\" class=\"menu_tab onglet\" style=\"padding-left: 3px;\">";$link = LINK . "species/nominal/" . $data['species'] . "/";$tab_link = array(	array("general/", __("General")),	array("sub_species/" . $data['sub_species'] . "/", __("Sub species")),	array("photo/", __("Photos")),);if ( $data['nb_breeder'] != 0 ){	$tab_link[] = array("breeder/", __("Breeders") . " (" . $data['nb_breeder'] . ')');}if ( $data['pending'] != 0 ){	$tab_link[] = array("pending/", __("Pending") . " (" . $data['pending'] . ')');}$url = explode("/", $_GET['path']);//patch to prevent bug with depraceted functionif ( empty($url[4]) || strstr($url[4], ">") ){	$url[4] = 'general';    $method = 'general';}foreach ( $tab_link as $tab ){	$sym_link = explode('/', $tab[0]);	if ( $sym_link[0] == $url[4] )	{		echo "<li id=\"species-" . $sym_link[0] . "\" class=\"selected\"><a href=\"" . $link . $tab[0] . "\"  data-target=\"".$id_node."\" data-link=\"species-" . $sym_link[0] . "\">" . $tab[1] . "</a></li>";		$method = $url[4];	}	else	{		echo "<li id=\"species-" . $sym_link[0] . "\"><a href=\"" . $link . $tab[0] . "\"  data-target=\"".$id_node."\" data-link=\"species-" . $sym_link[0] . "\">" . $tab[1] . "</a></li>";	}}/*  if ($data['in_wait'] != 0)  {  echo "<li class=\"underline\"><a href=\"" . LINK . "photo/admin_crop/id_species_main:" . $data['id_species'] . "/\" rel=\"nofollow\">" . __("In pending") . " (" . $data['in_wait'] . ")</a></li>";  } */echo "</ul>";echo '<div id="'.$id_node.'">';\glial\synapse\FactoryController::addNode("species",  $method, $data['param']);echo '</div>';echo "</div>";