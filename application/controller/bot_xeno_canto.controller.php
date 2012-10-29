<?php

class bot_xeno_canto extends controller {

	public $module_group = "BOT";

	function index() {
		
	}

	function admin_xeno_canto() {
		
		if (from() == "administration.controller.php")
		{
			$module['picture'] = "administration/screen-capture-7_normal.png";
			$module['name'] = "Xeno Canto";
			$module['description'] = __("Manage sounds from Xeno Canto's bot");
			return $module;
		}
	}

	function looking_for_song() {
		
		include_once(LIBRARY . "Glial/parser/xeno_canto/xeno_canto.php");
		include_once (LIB . "wlHtmlDom.php");
		
		$data = xeno_canto::get_all_link();

		file_put_contents(TMP.'grab/xeno_canto.txt', serialize($data));
		
		exit;
	}
	
	
	function get_song()
	{
		$_SQL = Singleton::getInstance(SQL_DRIVER);
		$data = unserialize(file_get_contents(TMP.'grab/xeno_canto.txt'));
		
		foreach($data as $elem)
		{
			
			if (stristr($elem['scientific_name'] , "sp."))
			{
				continue;
			}
			
			$sql = "SELECT * FROM species_main where scientific_name = '".$_SQL->sql_real_escape_string(trim($elem['scientific_name']))."'";
			$res = $_SQL->sql_query($sql);
			
			while ($ob = $_SQL->sql_fetch_object($res))
			{
				
				
				
				
				
				
				
				
				
				
				
				
			}
			
			if ($_SQL->sql_num_rows($res) == 0)
			{
				echo $elem['scientific_name']."\n";
			}
			
		}
		
		
		
		exit;
	}

}