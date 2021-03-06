<?php

use \Glial\I18n\I18n;

echo "<h3>" . __("Comments") . "</h3>";

if (!empty($data['comment'])) {

    echo '<table class="comment">';

    $i = 0;
    foreach ($data['comment'] as $comment) {
        $i++;
        ($i % 2 === 0) ? $couleur = "couleur2" : $couleur = "couleur1";
        echo '<tr class="' . $couleur . '">';
        echo '<td class="photo">';

        if (empty($comment['avatar'])) {
            echo '<img src="' . IMG . '/64/user3_64.png" height="64" widh="64" />';
        } else {
            echo '<img src="' . IMG . '/user/64/'.$comment['avatar'].'" height="64" widh="64" />';
        }

        echo '</td>';
        echo '<td class="comment-text">';


        echo '<div class="comment-author">';
        //echo '<span class="right"><a href="">'.__('Answer').'</a></span>';
        echo '<img src="' . IMG . 'country/type1/' . mb_strtolower($comment['iso'], 'utf-8') . '.gif" title="" width="18" height="12" alt="" /> ';
        echo '<a title="" href="' . LINK . 'user/profil/' . $comment['id'] . '"><b>' . $comment['firstname'] . ' ' . $comment['name'] . '</b></a>';
        echo ' ' . $comment['date'];
        echo '</div>';


        if ($comment['id_language'] !== I18n::Get() && $comment['comment'] == 1) {

            echo '<div class="comment-original">';
            echo '<img class="' . $comment['id_language'] . '" src="/backup/species/image/main/1x1.png" width="18" height="12" border="0"> ';
            //.__("Original :").
            echo $comment['text'] . "</div>";

            echo '<b>' . __("Translated automatically :") . "</b><br /> " . __($comment['text'], $comment['id_language']);
        } else {
            echo $comment['text'];
        }



        echo '</td>';


        echo "</tr>";
    }

    echo "</table>";
}


$post = LINK . "comment/image/" . $data['id_photo']."/".$data['nominal']."/";


//debug($post);

echo '<form action="' . $post . '" method="post">';
echo '<div class="post">
<table><tr><td>

<label class="required"><b>' . __("Language of message") . ' :</b>
<img id="flag" class="' . $data['default_lg'] . '" src="' . IMG . 'main/1x1.png" width="18" border="0" height="12">';

echo select("comment", "id_language", $data['geolocalisation_country'], $data['default_lg'], "textform lg");
echo '<br></label>

</td><td> <div style="margin-left:30px"><b>' . __("Subscribe?") . ' </b> <input type="checkbox" name="comment[subscribe]" checked="checked" />
' . __("Send me an email if this comment receives any replies") . '</div>

</td></tr></table>';

//echo '<label class="required"><strong>' . __("Comment") . ' <span>(' . __("Required") . ')</span></strong><br>';
echo textarea("comment", "text", "textform");
echo '<br>';
//echo '</label>';

echo '<input class="button btBlueTest overlayW btMedium" onclick="submit();" type="submit" value="' . __("Send") . '" /> ';
echo '</div>';


echo '<input type="hidden" name="comment[id]" value="' . $data['id_photo'] . '" />';
echo '</form>';
