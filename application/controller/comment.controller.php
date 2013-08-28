<?php

use glial\synapse\singleton;

class comment extends Controller {

	public $module_group = "Articles";

	function index() {
		
	}

	function image($param) {

		$_SQL = singleton::getInstance(SQL_DRIVER);

		if ($_SERVER['REQUEST_METHOD'] == "POST")
		{
			debug($_POST);
			//exit;

			if (!empty($_POST['comment']))
			{

				$comment = array();
				$comment['comment__species_picture_main']['id_user_main'] = $GLOBALS['_SITE']['IdUser'];
				$comment['comment__species_picture_main']['id_species_picture_main'] = $_POST['comment']['id'];
				$comment['comment__species_picture_main']['id_parent'] = NULL;
				$comment['comment__species_picture_main']['id_language'] = $_POST['comment']['id_language'];
				$comment['comment__species_picture_main']['date'] = date('c');
				$comment['comment__species_picture_main']['text'] = $_POST['comment']['text'];

				(empty($_POST['comment']['subscribe'])) ? $subscribe = 0 : $subscribe = 1;
				$comment['comment__species_picture_main']['subscribe'] = $subscribe;

				if ($_SQL->sql_save($comment))
				{
					$title = $GLOBALS['_LG']->getTranslation(__("Success"));
					$msg = $GLOBALS['_LG']->getTranslation(__("Your comment has been added."));

					set_flash("success", $title, $msg);
					header("location: " . LINK . "photo/admin_crop/id_photo:" . $_POST['comment']['id'] . '/');
					exit;
				}
				else
				{
					$title = $GLOBALS['_LG']->getTranslation(__("Error"));
					$msg = $GLOBALS['_LG']->getTranslation(__("Please review the following issues that occurred"));

					set_flash("error", $title, $msg);
					header("location: " . LINK . "photo/admin_crop/id_photo:" . $_POST['comment']['id'] . '/');
					exit;
				}

				debug($comment);
				debug($_SQL->sql_error());
				die();
			}
		}

		$this->layout = false;

		$sql = "SELECT * FROM comment__species_picture_main a
			INNER JOIN user_main b ON a.id_user_main = b.id
			INNER JOIN 	geolocalisation_country c ON b.id_geolocalisation_country = c.id
			WHERE a.id_species_picture_main = '" . $_SQL->sql_real_escape_string($param[0]) . "'";
		$res = $_SQL->sql_query($sql);
		$data['comment'] = $_SQL->sql_to_array($res);



		$_LG = singleton::getInstance("Language");
		$lg = explode(",", LANGUAGE_AVAILABLE);
		$nbchoice = count($lg);

		for ($i = 0; $i < $nbchoice; $i++)
		{
			$data['geolocalisation_country'][$i]['libelle'] = $_LG->languagesUTF8[$lg[$i]];
			$data['geolocalisation_country'][$i]['id'] = $lg[$i];
		}
		$data['default_lg'] = $_LG->Get();

		$data['id_photo'] = $param[0];

		$this->set("data", $data);
	}

}