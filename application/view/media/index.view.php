<?phpecho "<div id=\"species_detail\">";echo "<ul id=\"onglet\" class=\"menu_tab\" style=\"padding-left: 3px;\"><li id=\"general\" class=\"selected\"><a href=\"#".LINK."media/photo/\">".__("Photos")."</a></li><li id=\"sub_species\"><a href=\"#".LINK."media/video\">".__("Videos")."</a></li><li><a href=\"#ff\">".__("Articles")."</a></li><li><a href=\"#ff\">".__("News")."</a></li><li><a href=\"#ff\">".__("Sounds")."</a></li><li><a href=\"#ff\">".__("Articles")."</a></li></ul>";echo "<div id=\"content_1\">";$rr = new controller("media", "photo",json_encode(array("Erythrura_regia")));$rr->recursive = true;$rr->get_controller();echo "</div>";  //result.1echo "</div>";?>