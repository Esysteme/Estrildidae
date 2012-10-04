<?php
$_SQL = Singleton::getInstance(SQL_DRIVER);


$sql = "select count(1) as cpt from user_main";
$res = $_SQL->sql_query($sql);
$_SITE['NumberRegisters'] = $_SQL->sql_fetch_array($res);

$sql = "select count(1) as cpt from species_picture_main";
$res = $_SQL->sql_query($sql);
$_SITE['NumberPictures'] = $_SQL->sql_fetch_array($res);

$sql = "select count(1) as cpt from species_picture_in_wait a
	INNER JOIN species_tree_id b ON b.id_species_main = a.id_species_main
	WHERE b.id_species_family='438' and id_history_etat = 1";
$res = $_SQL->sql_query($sql);
$_SITE['NumberPicturesInWait'] = $_SQL->sql_fetch_array($res);

$sql = "select count(1) as cpt from species_genus a
	INNER JOIN species_main b ON b.id_species_genus = a.Id
	WHERE a.id_species_family='438' and a.is_valid!=0";
$res = $_SQL->sql_query($sql);
$_SITE['NumberEstrildidae'] = $_SQL->sql_fetch_array($res);

$sql = "SELECT count(1) as cpt from species_sub c
	INNER JOIN species_main b ON b.Id = c.id_species_main
	INNER JOIN species_genus a ON b.id_species_genus = a.Id
	WHERE a.id_species_family='438' and c.is_valid!=0";



$sql = "select count(1) as cpt from species_sub c
	INNER JOIN species_main b ON b.id = c.id_species_main
	INNER JOIN species_genus a ON b.id_species_genus = a.Id
	WHERE a.id_species_family='438'";

$res = $_SQL->sql_query($sql);
$_SITE['NumberEstrildidaeSsp'] = $_SQL->sql_fetch_array($res);





$sql = "select count(1) as cpt from species_sub";
$res = $_SQL->sql_query($sql);
$_SITE['NumberSsp'] = $_SQL->sql_fetch_array($res);



echo "<!DOCTYPE html>\n";
echo "<html lang=\"" . $_LG->Get() . "\">";

echo "<head>\n";
echo "<meta charset=utf-8 />\n";
echo "<meta name=\"Keywords\" content=\"Etrildidae,Estrildidés,梅花雀科,עסטרילדידאַע,forum,news,photos,videos,[PAGE_KEYWORDS]\" />\n";
echo "<meta name=\"Description\" content=\"[PAGE_DESCRIPTION]\" />\n";
echo "<meta name=\"Author\" content=\"Aurelien LEQUOY\" />\n";
echo "<meta name=\"robots\" content=\"index,follow,all\" />\n";
echo "<meta name=\"generator\" content=\"GLIALE 1.1\" />\n";
echo "<meta name=\"runtime\" content=\"[PAGE_GENERATION]\" />\n";


//echo "<link rel=\"shortcut icon\" href=\"pictures/main/favicon.ico\" />";


echo "<title>" . $GLIALE_TITLE . " - Estrildidae</title>\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . CSS . "style.css\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . CSS . "extjs.css\" />\n";
//echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"javascripts/ext-3.0.0/resources/css/ext-all.css\" />\n";
//echo "<link rel=\"shortcut icon\" href=\"esysteme/clavier.ico\" />\n";



if ($_SERVER['SERVER_ADDR'] != "192.168.1.48")
{
	echo '<script type="text/javascript">';

	echo "
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-34201303-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>";
}
?>
</head>


<body>


	<div id="all">


		<div id="headline">

<?php
echo __(Date("l")) . " " . Date("d") . " " . __(Date("F")) . " - " . Date("H:i:s") . " CET - 
	<a href=\"" . LINK . "user/\">" . __("Members") . "</a> : <strong>" . $_SITE['NumberRegisters']['cpt'] . "</strong>
 - <a href=\"\">" . __("Articles") . "</a> : <strong>0</strong> 
- <a href=\"" . LINK . "species/family/Estrildidae\">" . __("Species") . "</a> : <strong>" . $_SITE['NumberEstrildidae']['cpt'] . "</strong> 
- <a href=\"\">" . __("Subspecies") . "</a> : <strong>" . $_SITE['NumberEstrildidaeSsp']['cpt'] . "</strong> 
- <a href=\"" . LINK . "media/photo/\">" . __("Photos") . "</a> : <strong>" . $_SITE['NumberPictures']['cpt'] . " (+" . $_SITE['NumberPicturesInWait']['cpt'] . ")</strong> 
- <a href=\"\">" . __("Videos") . "</a> : <strong>0 (+0)</strong> 
- <a href=\"\">" . __("Articles") . "</a> : <strong>0 (+0)</strong>
";
?>


		</div>



		<div id="banner">

			<div class="terre">

				<embed src="<?php echo IMG ?>main/terre.swf" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" wmode="transparent" type="application/x-shockwave-flash" height="40" width="40"> 
				<noscript>
				<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="40" height="40"> 
					<param name="movie" value="<?php echo IMG ?>main/terre.swf" />
					<param name="quality" value="high" />
					<param name="wmode" value="transparent" /> 
					<embed src="<?php echo IMG ?>main/terre.swf" width="40" height="40" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" wmode="transparent"></embed>
				</object> </noscript>
				<!--<a style="margin-left:290px" href=""><img src="pictures/credits/468x60.png" height="60" width="468" />-->
			</div>

		</div>


		<div id="menu">



<?php
$menu = array(__("Home"), __("Species"), __("Medias"), __("Members"), __("Who we are?"), __("FAQ"), __("Partner"), __("Download"), __("Contact us"), __("Forum"));
$link = array("home/", "species/family/Estrildidae", "media/", "user/", "who_we_are/", "faq/", "partner/", "download/", "contact_us/", "forum/");

echo "<ul class=\"menu\">";



$i = 0;
foreach ($menu as $value)
{

	$tmp = explode("/", $link[$i]);

	if (strstr($_GET['url'], $tmp[0]) || ($_GET['url'] === "home/index/" && $i === 0))
	{
		$selected = "selected";
	}
	else
	{
		$selected = "";
	}
	echo "<li><a class=\"" . $selected . "\" href=\"" . LINK . $link[$i] . "\">" . $value . "</a></li>";
	$i++;
}



if (strstr($_GET['url'], "search/"))
{
	$selected = "selected";
}
else
{
	$selected = "";
}



echo "<li><form method=\"post\" action=\"" . LINK . "search/\"><span class=\"" . $selected . "\"><a href=\"" . LINK . "search/\">" . __("Search") . "</a>&nbsp;
	" . input("google_search", "key_words") . "
	</span></form></li>";
echo "</ul>";

echo "</div>";
echo "<div id=\"main\">";

echo "<div id=\"login\">";

echo "<div style=\"float:right;\">
		
		<div style=\"float:left; line-height:18px\">" . __("Language") . " :&nbsp;</div>
		<ul id=\"langage\">";
echo "<li class=\"top\"><span class=\"item_lg\"><img src=\"" . IMG . "language/" . $_LG->Get() . ".gif\" width=\"18\" height=\"12\" border=\"0\" />
		<span id=\"lg_main\"> " . $_LG->languagesUTF8[$_LG->Get()] . "</span></span><ul class=\"sub\">\n";



$lg = explode(",", LANGUAGE_AVAILABLE);
$nbchoice = count($lg);

for ($i = 0; $i < $nbchoice; $i++)
{


	if ($lg[$i] != $_LG->Get())
		echo "<li><a href=\"" . WWW_ROOT . $lg[$i] . "/" . $_GET['url'] . "\"><img class=\"" . $lg[$i] . "\" src=\"" . IMG . "main/1x1.png\" width=\"18\" height=\"12\" border=\"0\" /> " . $_LG->languagesUTF8[$lg[$i]] . "</a></li>\n";
}

echo "</ul>";
echo "</li>";
echo "</ul></div>";




$login = new controller("user", "login", "gg");
$login->get_controller();
$login->display();


/*
  for($i=0; $i< $nbchoice; $i++)
  {
  echo $_LG_choice[$i]." - ".base64_encode(fread(fopen(ROOT."/app/webroot/image/language/".$_LG_choice[$i].".gif", "r"), filesize(ROOT."/app/webroot/image/language/".$_LG_choice[$i].".gif")))."<br />";

  }
 */
?>
		</div>
		<div id="content">	
