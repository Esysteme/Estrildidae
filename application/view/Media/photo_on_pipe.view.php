<?php

function headerform()
{
    echo '</ul>';
    echo '<div class="clear"></div>';

    echo '</div>';
}

//debug($data);
echo '<div class="pending">';



$species = -1;
$author = -1;


foreach ($data['photos'] as $tab) {


    if ($species !== $tab['scientific_name']) {
        if ($species !== -1) {
            echo '</ul>';
            echo '<div class="clear"></div>';
        }
        echo "<h3>" . $tab['scientific_name'] . "</h3>";
        echo '<ul class="onlglet_pic">';

        $species = $tab['scientific_name'];
    }


    if ($author !== $tab['surname']) {
        if ($author !== -1) {
            echo '</ul>';
            echo '<div class="clear"></div>';
        }
        echo "<h4>" . $tab['surname'] . "</h4>";
        echo '<ul class="onlglet_pic">';

        $author = $tab['surname'];
    }


    echo '<li>';
    echo '<div class="selector img_norm">';
    echo '<img style="display:block;" title="' . $tab['scientific_name'] . '" src="' . str_replace("_s", "_q", $tab['miniature']) . '" height="150" width="150"  /> ';
    echo '<span><img style="display:block;" src="' . str_replace("_s", "_z", $tab['miniature']) . '"></span>';
    echo "</div>";
    echo '</li>';
}



echo "<div class=\"clear\"></div>";
echo "</div>";
?>