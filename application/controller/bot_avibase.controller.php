<?php

class bot_avibase extends controller {

	public $module_group = "BOT";

	function index() {
		
	}

	function test() {

		$_SQL = Singleton::getInstance(SQL_DRIVER);
		include_once(LIBRARY . "Glial/parser/avibase/avibase.php");
		include_once(LIBRARY . "Glial/species/species.php");
		include_once (LIB . "wlHtmlDom.php");


		$ret = avibase::get_species_by_reference();

		debug($ret);
		exit;
	}

	function get_infos_from_source() {


		include_once(LIBRARY . "Glial/parser/avibase/avibase.php");
		include_once(LIBRARY . "Glial/species/species.php");
		include_once (LIB . "wlHtmlDom.php");

		$_SQL = Singleton::getInstance(SQL_DRIVER);
		$sql = "SELECT id,reference_id FROM species_source_detail WHERE  id_species_source_main = 2";

		$res = $_SQL->sql_query($sql);


		$i = 0;
		while ($ob = $_SQL->sql_fetch_object($res)) {
			$i++;
			$ret = avibase::get_species_by_reference($ob->reference_id);

			$data = array();
			$data['species_source_data']['id_species_source_detail'] = $ob->id;
			$data['species_source_data']['type'] = "main";
			$data['species_source_data']['date'] = date("c");
			$data['species_source_data']['data'] = base64_encode(gzencode(json_encode($ret), 9));


			if (!$_SQL->sql_save($data))
			{
				debug($_SQL->sql_error());
				debug($data);
				die();
			}

			echo $i . " [" . date("Y-m-d H:i:s") . "] " . $ob->reference_id . " - " . $ret['Scientific'] . "\n";
		}
	}

	/*
	  CREATE TEMPORARY TABLE tmp_tbl (id int); # MySQL n'a retourné aucune ligne.
	  INSERT INTO tmp_tbl select min(id) from species_source_data group by id_species_source_detail, type having count(1)>1;
	  DELETE a FROM species_source_data a INNER JOIN tmp_tbl b ON a.id = b.id;
	 */

	function parse_get_infos() {
		$_SQL = Singleton::getInstance(SQL_DRIVER);
		$sql = "SELECT id_species_main,	id_species_sub,reference_id, data, b.id FROM species_source_detail a
		INNER JOIN 	species_source_data b ON a.id = b.id_species_source_detail 
		WHERE  a.id_species_source_main = 2 AND b.is_parsed=0";

		$res = $_SQL->sql_query($sql);


		$i = 0;


		while ($ob = $_SQL->sql_fetch_object($res)) {

			$i++;
			$data = json_decode(gzinflate(substr(base64_decode($ob->data), 10, -8)), true);

			echo "\n" . $i . " [" . date("Y-m-d H:i:s") . "] " . $data['Order'] . " - " . $data['Family'] . " - " . $data['Scientific'] . " : ";

			foreach ($data['Language'] as $lang => $text)
			{

				$lang = trim(str_replace("(Brazil)", "", $lang));

				$sql = "SELECT * FROM language WHERE print_name = '" . $lang . "'";
				$res2 = $_SQL->sql_query($sql);

				if ($_SQL->sql_num_rows($res2) == 1)
				{
					$ob2 = $_SQL->sql_fetch_object($res2);


					$this->insert_scientific_name_translation($ob->id_species_main, $ob->id_species_sub, $ob2->iso3, $text);

					echo $ob2->iso3 . " ";
				}
				else
				{
					echo "Number found : " . $_SQL->sql_num_rows($res2) . "\n";
					debug("$lang => $text");
					debug($_SQL->sql_error());
					die;
				}
			}

			foreach ($data['Synonyms'] as $lang => $tab)
			{

				$to_replace = array(", Southern", "(Brazil)", "(Colombia)", "(Venezuela)", "(Balears)", "(Uruguay)", "(Dominican Rep.)", " Creole French");
				$lang = trim(str_replace($to_replace, "", $lang));

				$sql = "SELECT * FROM language WHERE print_name = '" . $lang . "'";
				$res2 = $_SQL->sql_query($sql);

				if ($_SQL->sql_num_rows($res2) == 1)
				{

					$ob2 = $_SQL->sql_fetch_object($res2);
					foreach ($tab as $text)
					{
						$this->insert_scientific_name_translation($ob->id_species_main, $ob->id_species_sub, $ob2->iso3, $text);

						echo $ob2->iso3 . " ";
					}
				}
				else
				{
					debug("undefined : " . $lang);
					debug($_SQL->sql_error());
					die;
				}
			}


			$data = array();
			$data['species_source_data']['id'] = $ob->id;
			$data['species_source_data']['is_parsed'] = 1;
			$_SQL->sql_save($data);
		}

		exit;
	}

	function insert_scientific_name_translation($id_species, $id_species_sub, $lang, $text) {
		//22720



		$_SQL = Singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT id FROM scientific_name_translation 
			WHERE id_species_main = '" . $_SQL->sql_real_escape_string($id_species) . "'
			AND id_species_sub = '" . $_SQL->sql_real_escape_string($id_species_sub) . "'
			AND language = '" . $_SQL->sql_real_escape_string($lang) . "'
			AND text = '" . $_SQL->sql_real_escape_string($text) . "'";

		$res = $_SQL->sql_query($sql);

		if ($_SQL->sql_num_rows($res) == 0)
		{
			$sql = "SELECT count(1) as cpt FROM scientific_name_translation 
			WHERE id_species_main = '" . $_SQL->sql_real_escape_string($id_species) . "'
			AND id_species_sub = '" . $_SQL->sql_real_escape_string($id_species_sub) . "'
			AND is_valid = 1";

			$res = $_SQL->sql_query($sql);
			$ob = $_SQL->sql_fetch_object($res);

			$data = array();
			$data['scientific_name_translation']['id_species_main'] = $id_species;
			$data['scientific_name_translation']['id_species_sub'] = $id_species_sub;
			$data['scientific_name_translation']['language'] = $lang;
			$data['scientific_name_translation']['text'] = $text;
			if ($ob->cpt == 0)
			{
				$data['scientific_name_translation']['is_valid'] = 1;
			}
			else
			{
				$data['scientific_name_translation']['is_valid'] = 0;
			}

			$id_ret = $_SQL->sql_save($data);

			if (!$id_ret)
			{
				debug($id_ret);
				debug($_SQL->sql_error());
				die();
			}
		}
		else
		{
			$ob = $_SQL->sql_fetch_object($res);
			return $ob->id;
		}
	}

	function update_language() {
		$_SQL = Singleton::getInstance(SQL_DRIVER);

		$charset = array(
			"zh-cn" => "GB2312",
			"hr" => "croatian",
			"cs" => "Windows-1252",
			"da" => "ISO-8859-1",
			"nl" => "ISO-8859-1",
			"en" => "ISO-8859-1",
			"fi" => "ISO-8859-1",
			"fr" => "ISO-8859-1",
			"de" => "ISO-8859-1",
			"it" => "ISO-8859-1",
			"ja" => "Shift_JIS",
			"ko" => "EUC-KR",
			"no" => "ISO-8859-1",
			"pl" => "ISO-8859-2",
			"pt" => "ISO-8859-1",
			"ru" => "KOI8-R",
			"es" => "ISO-8859-1"
		);

		foreach ($charset as $key => $value)
		{
			$sql = "UPDATE language SET charset = '" . $_SQL->sql_real_escape_string($value) . "' WHERE iso = '" . $key . "'";
			//$sql = "INSERT IGNORE language (iso) values ('".$key."')";

			$_SQL->sql_query($sql);
		}

		exit;
	}

	function update_language2() {

		$_SQL = Singleton::getInstance(SQL_DRIVER);
		$tab = file("lang.csv");

		foreach ($tab as $value)
		{
			$ob = explode(";", $value);
			echo $ob['6']."\n";

			$sql = "UPDATE language SET print_name = '" . $_SQL->sql_real_escape_string($ob['6']) . "' WHERE iso3 = '" . $ob[0] . "'";
			$_SQL->sql_query($sql);
		}
	}

}