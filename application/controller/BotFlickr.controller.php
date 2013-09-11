<?php

use \glial\parser\flickr\Flickr;
use \glial\synapse\Singleton;
use \glial\shell\Color;


use \glial\synapse\Controller;

class botflickr extends Controller
{

	public $module_group = "BOT";

	function index()
	{
		
	}

	function admin_flickr()
	{


		if ( from() == "administration.controller.php" )
		{
			$module['picture'] = "administration/flickr.png";
			$module['name'] = "Flickr";
			$module['description'] = __("Manage picture importation from Flickr's bot");
			return $module;
		}
		$_SQL = singleton::getInstance(SQL_DRIVER);
		$this->layout_name = 'admin';


		$sql = "SELECT *,(select count(1) from species_picture_id d where d.photo_id = b.photo_id group by d.photo_id) as cpt
			FROM species_picture_search a 
			inner join species_picture_id b ON a.id = b.id_species_picture_search
			WHERE a.id_species_main = 9222";

		$res = $_SQL->sql_query($sql);
		$data['img'] = $_SQL->sql_to_array($res);

		$this->set('data', $data);
	}

	function family()
	{

		$res = $GLOBALS['_SQL']->sql_query($sql);


		while ( $ob = $GLOBALS['_SQL']->sql_fetch_object($res) )
		{
			
		}
	}

	private function get_licence($licence)
	{
		switch ( $licence['text'] )
		{
			case "Tous droits réservés":
			case "All Rights Reserved":
				$id_licence = 1;
				break;

			case "Certains droits réservés (licence Creative Commons)":
				switch ( $licence['url'] )
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

	function looking_for_family()
	{
		
		include_once (LIB . "wlHtmlDom.php");

		$_SQL = singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT id_nominal , nominal,b.*  from species_tree_nominal a
			LEFT JOIN species_translation b ON a.id_nominal = b.id_row AND b.id_table = 7
where a.id_family = 438 order by rand()";

		$res = $_SQL->sql_query($sql);

		while ( $ob = $_SQL->sql_fetch_object($res) )
		{
			$tab_name = array($ob->nominal);

			//, $ob->fr, $ob->en, $ob->de, $ob->es, $ob->nl, $ob->it, $ob->ja, $ob->cs, $ob->pl, $ob->fi, $ob->da, $ob->no, $ob->sk);

			foreach ( $tab_name as $name )
			{
				if ( !empty($name) )
				{
					$data['link_photo'] = flickr::get_links_to_photos($name);

					foreach ( $data['link_photo'] as $url_to_get )
					{
						$data['img'] = flickr::get_photo_info($url_to_get);

						if ( $data['img'] )
						{
							$tmp = array();
							$sql = "SELECT count(1) as cpt, id FROM species_picture_in_wait where photo_id = '" . $data['img']['id'] . "'";
							$res2 = $_SQL->sql_query($sql);
							$ob2 = $_SQL->sql_fetch_object($res2);

							if ( $ob2->cpt != 0 )
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


							if ( trim($data['img']['image']['mime']) === "image/jpeg" )
							{
								$GLOBALS['_SQL']->set_history_user(9);


								if ( !$_SQL->sql_save($tmp) )
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

	function update_search()
	{
		;
		include_once (LIB . "wlHtmlDom.php");

		

		$_SQL = Singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT id_nominal , a.nominal,b.id_species_sub,   b.language, b.text as name
		from species_tree_nominal a
		INNER JOIN scientific_name_translation b ON a.id_nominal= b.id_species_main
		INNER JOIN language c ON b.language = c.iso3
		LEFT JOIN species_picture_search d ON d.tag_search = b.text AND c.iso3 = d.language
		where a.id_family = 438 AND b.id_species_sub = 0 AND d.total_found is null
		order by rand()";

		$res = $_SQL->sql_query($sql);

		while ( $ob = $_SQL->sql_fetch_object($res) )
		{

			echo Color::getColoredString($ob->nominal, "black", "green") . "\n";


			$data['link_photo'] = Flickr::get_links_to_photos($ob->name);

			$search = array();
			$search['species_picture_search']['id_species_main'] = $ob->id_nominal;
			$search['species_picture_search']['tag_search'] = $ob->name;
			$search['species_picture_search']['language'] = $ob->language;
			$search['species_picture_search']['total_found'] = (int) count($data['link_photo']);
			$search['species_picture_search']['id_user_main'] = 9;
			$search['species_picture_search']['date'] = date('Y-m-d H:i:s');

			$id_search = $_SQL->sql_save($search);

			if ( $id_search )
			{
				echo '[';
				$i=1;
				foreach ( $data['link_photo'] as $url_to_get )
				{
					//debug( $url_to_get);
					echo $i.", ";
					$i++;
					
					$author = array();
					$author['species_author']['surname'] = $url_to_get['author'];
					$author['species_author']['date'] = date('Y-m-d H:i:s');

					$id_author = $_SQL->sql_save($author);

					if (! $id_author )
					{
						//debug($author);
						debug($_SQL->sql_error());
						echo Color::getColoredString("Impossible to insert this author", "white", "red") . "\n";
					}
					
					//delete from species_author where id > 1600
					
					
					if ( empty($url_to_get['img']['url']) )
					{
						print_r($url_to_get);
						die("pb pic");
					}

					$pic_id = array();
	
					$pic_id['species_picture_id']['photo_id'] = flickr::get_photo_id($url_to_get['url']);
					$pic_id['species_picture_id']['id_species_author'] = $id_author;
					$pic_id['species_picture_id']['link'] = $url_to_get['url'];
					$pic_id['species_picture_id']['miniature'] = $url_to_get['img']['url'];
					$pic_id['species_picture_id']['date'] = date('Y-m-d H:i:s');

					//debug($pic_id);
					
					$id_picture = $_SQL->sql_save($pic_id);
					
					if ( ! $id_picture)
					{
						//debug($pic_id);
						debug($_SQL->sql_error());
						
						echo Color::getColoredString("Impossible to insert picture", "white", "red") . "\n";
					}
					else
					{
						$pic_id = array();
						$pic_id['link__species_picture_id__species_picture_search']['id_species_picture_id'] = $id_picture;
						$pic_id['link__species_picture_id__species_picture_search']['id_species_picture_search'] = $id_search;
						$pic_id['link__species_picture_id__species_picture_search']['date'] = date('Y-m-d H:i:s');

						if ( !$_SQL->sql_save($pic_id) )
						{
							//debug($pic_id);
							debug($_SQL->sql_error());
							echo Color::getColoredString("ERROR INSERTION link__species_picture_id__species_picture_search", "white", "red") . "\n";
							
						}
					}
				}
				echo "]\n";
			}
			else
			{
				
				debug($_SQL->sql_error());
				echo Color::getColoredString("ERROR INSERTION species_picture_search", "white", "red") . "\n";
			}
		}

		exit;
	}

	function test()
	{

		$this->layout_name = false;

		include_once (LIB . "wlHtmlDom.php");

		$data = Flickr::getLinksToPhotos("lonchura montana");

		print_r($data);

		//http://farm8.staticflickr.com/7022/6657652857_34d38960ab_z.jpg
		//http://farm8.staticflickr.com/7022/6657652857_34d38960ab_b.jpg

		exit;
	}

	function test2()
	{

		$this->layout_name = false;
		include_once(LIBRARY . "Glial/parser/flickr/flickr.php");
		include_once (LIB . "wlHtmlDom.php");


		$url = "http://www.flickr.com/photos/75299599@N00/6657652857/";
		$url = "http://www.flickr.com/photos/81609886@N05/9304372638/in/photostream/";
		$url2 = "http://www.flickr.com/photos/gregbm/map/?photo=6657652857";

		$data = flickr::getPhotoInfo($url);
		//$data = flickr::get_gps($url2);

		print_r($data);


		exit;
	}

	function import_geolocalisation()
	{
		$this->layout_name = false;
		include_once(LIBRARY . "Glial/parser/flickr/flickr.php");
		include_once (LIB . "wlHtmlDom.php");
		$_SQL = Singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT * FROM species_picture_main where data != ''";

		$res = $_SQL->sql_query($sql);

		$i = 0;
		while ( $ob = $_SQL->sql_fetch_object($res) )
		{

			$data = unserialize(base64_decode($ob->data));


			if ( !empty($data['gps']['latitude']) && $data['gps']['latitude'] != 0 )
			{
				$i++;
				echo $i . " [" . date("Y-m-d H:i:s") . "] photo : " . $data['url'] . "\n";

				$sql = "UPDATE species_picture_main SET latitude = '" . $data['gps']['latitude'] . "', longitude = '" . $data['gps']['longitude'] . "' WHERE id = '" . $ob->id . "'";
				$_SQL->sql_query($sql);

				/*
				  $pic = array();

				  $pic['species_picture_main']['id'] = $ob->id;
				  $pic['species_picture_main']['latitude'] = $data['gps']['latitude'];
				  $pic['species_picture_main']['longitude'] = $data['gps']['longitude'];

				  echo $i . " [" . date("Y-m-d H:i:s") . "] photo : ".$data['url']."\n";

				  if (! $_SQL->sql_save($pic))
				  {
				  debug($pic);
				  debug($_SQL->sql_error());
				  die();
				  }

				  unset($pic);
				 */
			}
		}
	}
	
	
	function displayPicture($id_species)
	{
		$sql = "SELECT * FROM ";
		
	}

}

