<?php






echo "<h3 class=\"item\">".__("Formulaire de contact","fr")."</h3>";


echo "<form action=\"\" method=\"post\">";
echo "<table class=\"form\" width=\"100%\">";

echo "<tr>";
echo "<td class=\"first\">".__("Email")." :</td>";
echo "<td>".input("user_main","email","textform")."</td>";
echo "</tr>";
echo "<tr>";
echo "<td class=\"first\">".__("Name")." :</td>";
echo "<td>".input("user_main","name","textform")."</td>";
echo "</tr>";
echo "<tr>";
echo "<td class=\"first\">".__("Firstname")." :</td>";
echo "<td>".input("user_main","firstname","textform")."</td>";
echo "</tr>";




echo "<tr>";
echo "<td class=\"first\">".__("Country")." :</td>";
echo "<td>".select("user_main","id_geolocalisation_country",$data['geolocalisation_country'],"","textform")."</td>";
echo "</tr>";
echo "<tr>";
echo "<td class=\"first\">".__("City")." :</td>";
echo "<td>".autocomplete("user_main","id_geolocalisation_city","textform")."</td>";
echo "</tr>";




/*
echo "<tr>";
echo "<td>Identifiant </td>";
echo "<td>: <input class=\"text\" type=\"text\" name=\"identifiant\" value=\"".$_GET['identifiant']."\" /></td>";
echo "</tr>";
*/
echo "<tr>";
echo "<td class=\"first\">".__("Message")." :</td>";
echo "<td><textarea class=\"textform\"></textarea></td>";
echo "</tr>";


echo "<tr>";
echo "<td colspan=\"2\" class=\"td_bouton\"><br/><input class=\"button btBlueTest overlayW btMedium\" type=\"submit\" value=\"".__('Send !')."\" /> <input class=\"button btBlueTest overlayW btMedium\" type=\"reset\" value=\"".__('Delete')."\" /></td>";
echo "</tr>";
echo "</table>";


echo "</form>";
