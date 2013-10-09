<?php

$id_node = "node-" . uniqid();


$url = explode("/", $_GET['path']);



// get all option avaible
$value_menu = array();
foreach ($data['pending'] as $tab2) {
    $value_menu[] = $tab2['name'];
}




if (count($data['pending']) > 0) {

    //in case where none selected we take first item
    if (!in_array($url[5], $value_menu)) {
        $url[5] = $data['pending'][0]['name'];

        //withh be used for addNode
        $data['param'][2] = "sort";
        $data['param'][3] = $data['pending'][0]['name'];
    }

    echo "<ul id=\"\" class=\"menu_tab onglet\" style=\"padding-left: 3px;\">";

    $i = 0;

    foreach ($data['pending'] as $tab) {
        if (($url[5] === $tab['name'])) {
            echo '<li id="' . $tab['name'] . '" class="selected">';
            $url[5] = -1;
        } else {
            echo '<li id="' . $tab['name'] . '">';
        }

        echo '<a href="' . LINK . "species/nominal/" . $data['param'][0] . "/pending/sort/" . $tab['name'] . '/" data-target="' . $id_node . '" data-link="species-sort" style="padding-left:24px"><img src="' . IMG . '16/' . $tab['pic16'] . '" width="16" height="16" />' . $tab['name'] . ' (' . $tab['cpt'] . ')</a>';
        echo '</li>';
    }
    echo '<li class="right">';
    echo '<a href="' . LINK . '"species/nominal/" data-target="' . $id_node . '" data-link="species-sort" style="padding-left:24px">Crop</a>';
    echo '</li>';
    echo "</ul>";
}



echo '<div id="' . $id_node . '">';
\Glial\Synapse\FactoryController::addNode("species", "sort", $data['param']);
echo '</div>';




