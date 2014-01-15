<?php

if (count($out['forum'][2]) > 1) {
    foreach ($out['category'] as $cat) {
        echo "<div class=\"forum\">";
        echo "<table>";

        echo '<thead>
			<tr>
				<th scope="col" class="tcl">' . $cat['name'] . '</th>
				<th scope="col" class="tc2">' . __("Topics") . '</th>
				<th scope="col" class="tc3">' . __("Posts") . '</th>
				<th scope="col" class="tcr">' . __("Last post") . '</th>
			</tr>
	</thead>';


        foreach ($out['forum'][$cat['id']] as $forum) {
            /*
              if ($cat['id'] == 2)
              {
              $link = "0-".$out['tree_id']."-".$forum['id'];
              }
              else
              {
              $link = $forum['id']."-".$out['tree_id'];
              } */
            $link = $out['path'] . "-" . $forum['id'];


            if (empty($forum['description'])) {
                $forum['description'] = __("Any discution about") . " " . $forum['name'];
            }

            echo '
		<tbody>
			<tr class="rowodd">
				<td class="tcl">
					<div class="icon"><div class="nosize">1</div></div>
						<div class="tclcon">
							<div>
								<h4><a href="' . LINK . 'forum/index/' . $link . '/">' . $forum['name'] . '</a></h4>
								<div class="forumdesc">' . $forum['description'] . '</div>
							</div>
						</div>
				</td>
				<td class="tc2">' . $forum['cpt_topic'] . '</td>
				<td class="tc3">' . $forum['cpt_post'] . '</td>
				<td class="tcr"><a href="viewtopic.php?pid=42881#p42881">2011-10-19 00:01:56</a><br /><span class="byuser">by Franz</span></td>
			</tr>
		</tbody>';
        }

        echo "</table>";
        echo "</div>";
    }
}	
