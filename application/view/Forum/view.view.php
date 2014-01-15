<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo "<div class=\"forum\">";


echo '
<div class="pagepost">
<a class="button btBlueTest overlayW btMedium" href="'.LINK.'forum/post/">New topic</a><a class="button btBlueTest overlayW btMedium" href="'.LINK.'forum/post/">New reply</a><br /><br />
</div>';


echo "<table>";

echo '<thead>
		<tr>
			<th scope="col" class="tcl">' . __("Author") . '</th>
			<th scope="col" class="tcr">' . __("Message") . '</th>
		</tr>
</thead>';

foreach ($data['view'] as $data) {
    echo '<tr>';

    echo '<td style="border-right:#ddd 1px solid"><a href="' . LINK . 'user/profil/' . $data['id_user_main'] . '">' . $data['firstname'] . "" . $data['name'] . '</a>
    <br />';
    if (empty($data['avatar'])) {
        echo '<img src="' . IMG . '/64/user3_64.png" height="64" widh="64" />';
    } else {
        echo '<img src="' . IMG . '/user/64/' . $data['avatar'] . '" height="64" widh="64" />';
    }

    echo '<br /></td>';
    echo '<td><div>' . __('Posted the') . ' : ' . $data['posted'] . ' CET
        <span class="right"><a class="button btBlueTest overlayW btMedium" href="'.LINK.'forum/post/">'.__('citer').'</a>
            <a class="button btBlueTest overlayW btMedium" href="'.LINK.'forum/post/">'.__("Edit").'</a></span></div><div class="clear"></div>
                <hr />
                <br />' . $data['message'] . '</td>';
    echo '</tr>';
}


echo "</table>";


echo '<br />
<div class="pagepost">
<a class="button btBlueTest overlayW btMedium" href="'.LINK.'forum/post/">New topic</a><a class="button btBlueTest overlayW btMedium" href="'.LINK.'forum/post/">New reply</a><br /><br />
</div>';

echo '</div>';

