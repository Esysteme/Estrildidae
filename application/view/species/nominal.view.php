<?phpecho "<div id=\"species_detail\">";echo "<ul id=\"onglet\" class=\"menu_tab\" style=\"padding-left: 3px;\">";$link = LINK . "species/nominal/" . $data['species'] . "/";$tab_link = array(	array("general/", __("General")),	array("sub_species/" . $data['sub_species'] . "/", __("Sub species")),	array("photo/", __("Photos")),	);if ($data['nb_breeder'] != 0){	$tab_link[] = array("breeder/", __("Breeders")." (".$data['nb_breeder'].')');}if ($data['pending'] != 0){	$tab_link[] = array("waiting/", __("Pending")." (".$data['pending'].')');}$tab_link[] = array("test/", __("Test"));$url = explode("/", $_GET['path']);//patch to prevent bug with depraceted functionif (empty($url[4]) || strstr($url[4], ">")){	$url[4] = 'general';}foreach ($tab_link as $tab){	$sym_link = explode('/', $tab[0]);	if ($sym_link[0] == $url[4])	{		echo "<li id=\"" . $sym_link[0] . "\" class=\"selected\"><a href=\"" . $link . $tab[0] . "\">" . $tab[1] . "</a></li>";		$method = $sym_link[0];	}	else	{		echo "<li id=\"" . $sym_link[0] . "\"><a href=\"" . $link . $tab[0] . "\">" . $tab[1] . "</a></li>";	}}if ($data['in_wait'] != 0){	echo "<li class=\"underline\"><a href=\"" . LINK . "photo/admin_crop/id_species_main:" . $data['id_species'] . "/\" rel=\"nofollow\">" . __("In pending") . " (" . $data['in_wait'] . ")</a></li>";}echo "</ul>";echo "<div id=\"content_1\">";//$rr = new controller("species", "general",json_encode(array("Erythrura_regia")));//$sub = array($data['species'],'sub_species',$data['sub_species'],'sub_species>content_1');//$rr = new controller("species", "sub_species",json_encode($sub));$sub = $data['param'];$rr = new controller("species", $method, json_encode($sub));$rr->recursive = true;$rr->get_controller();echo "</div>";  //result.1echo "</div>";