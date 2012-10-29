<?php

class botflickr extends controller {

	public $module_group = "BOT";

	function index() {
		
	}

	function admin_flickr() {


		if (from() == "administration.controller.php")
		{
			$module['picture'] = "administration/flickr.png";
			$module['name'] = "Flickr";
			$module['description'] = __("Manage picture importation from Flickr's bot");
			return $module;
		}
		$_SQL = Singleton::getInstance(SQL_DRIVER);
		$this->layout_name = 'admin';
		
		
		$sql = "SELECT *,(select count(1) from species_picture_id d where d.photo_id = b.photo_id group by d.photo_id) as cpt
			FROM species_picture_search a 
			inner join species_picture_id b ON a.id = b.id_species_picture_search
			WHERE a.id_species_main = 9222";
		
		$res = $_SQL->sql_query($sql);
		$data['img'] = $_SQL->sql_to_array($res);
		
		$this->set('data', $data);
		
		
		
	}

	function family() {

		$res = $GLOBALS['_SQL']->sql_query($sql);


		while ($ob = $GLOBALS['_SQL']->sql_fetch_object($res)) {
			
		}
	}

	private function get_licence($licence) {
		switch ($licence['text'])
		{
			case "Tous droits réservés":
			case "All Rights Reserved":
				$id_licence = 1;
				break;

			case "Certains droits réservés (licence Creative Commons)":
				switch ($licence['url'])
				{
					case "http://creativecommons.org/licenses/by/2.0/": $id_licence = 5;
						break;
					case "http://creativecommons.org/licenses/by-sa/2.0/": $id_licence = 6;
						break;
					case "http://creativecommons.org/licenses/by-nd/2.0/": $id_licence = 7;
						break;
					case "http://creativecommons.org/licenses/by-nc/2.0/": $id_licence = 8;
						break;
					case "http://creativecommons.org/licenses/by-nc-sa/2.0/": $id_licence = 9;
						break;
					case "http://creativecommons.org/licenses/by-nc-nd/2.0/": $id_licence = 10;
						break;
					default:
						echo "- " . $licence['url'] . " - ";
						die("need to add a new license CC");
						break;
				}
				break;

			default:
				$id_licence = 11;
				break;
		}

		return $id_licence;
	}

	function looking_for_family() {
		include_once(LIBRARY . "Glial/parser/flickr/flickr.php");
		include_once (LIB . "wlHtmlDom.php");

		$_SQL = Singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT id_nominal , nominal,b.*  from species_tree_nominal a
			LEFT JOIN species_translation b ON a.id_nominal = b.id_row AND b.id_table = 7
where a.id_family = 438 order by rand()";

		$res = $_SQL->sql_query($sql);

		while ($ob = $_SQL->sql_fetch_object($res)) {
			$tab_name = array($ob->nominal, $ob->fr, $ob->en, $ob->de, $ob->es, $ob->nl, $ob->it, $ob->ja, $ob->cs, $ob->pl, $ob->fi, $ob->da, $ob->no, $ob->sk);

			foreach ($tab_name as $name)
			{
				if (!empty($name))
				{
					$data['link_photo'] = flickr::get_links_to_photos($name);

					foreach ($data['link_photo'] as $url_to_get)
					{
						$data['img'] = flickr::get_photo_info($url_to_get);

						if ($data['img'])
						{
							$tmp = array();
							$sql = "SELECT count(1) as cpt, id FROM species_picture_in_wait where photo_id = '" . $data['img']['id'] . "'";
							$res2 = $_SQL->sql_query($sql);
							$ob2 = $_SQL->sql_fetch_object($res2);

							if ($ob2->cpt != 0)
							{
								echo "New photo found on : " . $url_to_get . "\n";
								$tmp['species_picture_in_wait']['id'] = $ob2->id;
								$GLOBALS['_SQL']->set_history_type(13);
							}
							else
							{
								echo "Update photo found on : " . $url_to_get . "\n";
								$tmp['species_picture_in_wait']['id_history_etat'] = 1;
								$GLOBALS['_SQL']->set_history_type(3);
							}

							$tmp['species_picture_in_wait']['photo_id'] = $data['img']['id'];
							$tmp['species_picture_in_wait']['data'] = base64_encode(serialize($data['img']));
							$tmp['species_picture_in_wait']['id_licence'] = $this->get_licence($data['img']['license']);
							$tmp['species_picture_in_wait']['id_species_main'] = $ob->id_nominal;
							$tmp['species_picture_in_wait']['md5'] = $data['img']['image']['md5'];
							$tmp['species_picture_in_wait']['url_md5'] = md5($url_to_get);
							$tmp['species_picture_in_wait']['url_found'] = $data['img']['photo'];
							$tmp['species_picture_in_wait']['url_context'] = $url_to_get;
							$tmp['species_picture_in_wait']['date_created'] = date('c');


							$tmp['species_picture_in_wait']['author'] = $data['img']['author'];
							$tmp['species_picture_in_wait']['legend'] = $data['img']['legend'];
							$tmp['species_picture_in_wait']['title'] = $data['img']['title'];
							$tmp['species_picture_in_wait']['height'] = intval($data['img']['image']['height']);
							$tmp['species_picture_in_wait']['width'] = intval($data['img']['image']['width']);
							$tmp['species_picture_in_wait']['name'] = $data['img']['name'];

							$tmp['species_picture_in_wait']['location'] = $data['img']['location'];
							$tmp['species_picture_in_wait']['latitude'] = $data['img']['latitude'];
							$tmp['species_picture_in_wait']['longitude'] = $data['img']['longitude'];


							if (trim($data['img']['image']['mime']) === "image/jpeg")
							{
								$GLOBALS['_SQL']->set_history_user(9);


								if (!$_SQL->sql_save($tmp))
								{
									echo "#####################";
									debug($GLOBALS['_SQL']->sql_error());
									//die("Problem insertion data dans species_picture_in_wait");
									sleep(5);
								}
							}
							else
							{
								echo 'mine not good :' . $data['img']['image']['mime'] . "\n";
								sleep(5);
							}
						}
						else
						{
							echo "problem to get img !\n";
						}
						sleep(3);
					}
				}
			}
		}
// select * from 
	}

	function update_search() {
		include_once(LIBRARY . "Glial/parser/flickr/flickr.php");
		include_once (LIB . "wlHtmlDom.php");

		$_SQL = Singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT id_nominal , nominal,b.*  from species_tree_nominal a
			LEFT JOIN species_translation b ON a.id_nominal = b.id_row AND b.id_table = 7
where a.id_family = 438 order by rand()";

		$res = $_SQL->sql_query($sql);

		while ($ob = $_SQL->sql_fetch_object($res)) {
			$tab_name = array($ob->nominal, $ob->fr, $ob->en, $ob->de, $ob->es, $ob->nl, $ob->it, $ob->ja, $ob->cs, $ob->pl, $ob->fi, $ob->da, $ob->no, $ob->sk);
			$tab_lg = array("la", "fr", "en", "de", "es", "nl", "it", "ja", "cs", "pl", "fi", "da", "no", "sk");

			$i = 0;
			foreach ($tab_name as $name)
			{
				if (!empty($name))
				{
					$data['link_photo'] = flickr::get_links_to_photos($name);

					$search = array();
					$search['species_picture_search']['id_species_main'] = $ob->id_nominal;
					$search['species_picture_search']['tag_search'] = $name;
					$search['species_picture_search']['language'] = $tab_lg[$i];
					$search['species_picture_search']['total_found'] = (int) count($data['link_photo']);
					$search['species_picture_search']['id_user_main'] = 9;
					$search['species_picture_search']['date'] = date("c");

					$id_search = $_SQL->sql_save($search);

					if ($id_search)
					{
						foreach ($data['link_photo'] as $url_to_get)
						{
							$pic_id = array();
							$pic_id['species_picture_id']['id_species_picture_search'] = $id_search;
							$pic_id['species_picture_id']['photo_id'] = flickr::get_photo_id($url_to_get['url']);
							$pic_id['species_picture_id']['link'] = $url_to_get['url'];
							$pic_id['species_picture_id']['miniature'] = $url_to_get['img']['url'];
							$pic_id['species_picture_id']['width'] = $url_to_get['img']['width'];
							$pic_id['species_picture_id']['height'] = $url_to_get['img']['height'];
							$pic_id['species_picture_id']['author'] = $url_to_get['author'];

							if (!$_SQL->sql_save($pic_id))
							{
								debug($pic_id);
								debug($_SQL->sql_error());
							}
						}
					}
					else
					{
						debug($search);
						debug($_SQL->sql_error());
					}
				}

				$i++;
			}
		}

		exit;
	}

	function test() {

		$this->layout_name = false;
		include_once(LIBRARY . "Glial/parser/flickr/flickr.php");
		include_once (LIB . "wlHtmlDom.php");

		$data = flickr::get_links_to_photos("lonchura atricapilla");


		print_r($data);


		//http://farm8.staticflickr.com/7022/6657652857_34d38960ab_z.jpg
		//http://farm8.staticflickr.com/7022/6657652857_34d38960ab_b.jpg




		exit;
	}

}
