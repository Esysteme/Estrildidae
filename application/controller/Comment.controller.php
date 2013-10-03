<?php


use \Glial\I18n\I18n;
use \Glial\Synapse\Controller;


class Comment extends Controller {

	public $module_group = "Articles";

	function index() {
		
	}

	function image($param) {

		

		if ($_SERVER['REQUEST_METHOD'] == "POST")
		{

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

				if ($this->db['mysql_write']->sql_save($comment))
				{
					$title = I18n::getTranslation(__("Success"));
					$msg = I18n::getTranslation(__("Your comment has been added."));

					set_flash("success", $title, $msg);
					header("location: " . LINK . "species/nominal/".str_replace(' ','_',$param[1])."/photo/photo_detail/".$param[0]."/");
					exit;
				}
				else
				{
					$title = I18n::getTranslation(__("Error"));
					$msg = I18n::getTranslation(__("The comment can't be let empty"));

					set_flash("error", $title, $msg);
                    header("location: " . LINK . "species/nominal/".str_replace(' ','_',$param[1])."/photo/photo_detail/".$param[0]."/");
					//header("location: " . LINK . "photo/admin_crop/id_photo:" . $_POST['comment']['id'] . '/');
					exit;
				}

				debug($comment);
				debug($this->db['mysql_write']->sql_error());
				die();
			}

		}

		$this->layout = false;

		$sql = "
 (SELECT b.id,b.name,b.firstname,b.avatar, c.iso, a.date,a.text,a.id_language, 1 as comment FROM comment__species_picture_main a
			INNER JOIN user_main b ON a.id_user_main = b.id
			INNER JOIN 	geolocalisation_country c ON b.id_geolocalisation_country = c.id
			WHERE a.id_species_picture_main = '" . $this->db['mysql_write']->sql_real_escape_string($param[0]) . "')
           
UNION
(SELECT f.id,e.name,  e.firstname, e.avatar,h.iso ,d.date, f.title as text, 'en' as id_language, 0 as comment
			FROM history_main d
			INNER JOIN user_main e ON d.id_user_main = e.id
			INNER JOIN history_action f ON f.id = d.id_history_action
			INNER JOIN geolocalisation_country h ON e.id_geolocalisation_country = h.id
			WHERE line = '" . $this->db['mysql_write']->sql_real_escape_string($param[0]) . "')
                
			order by `date`

";
		$res = $this->db['mysql_write']->sql_query($sql);
		$data['comment'] = $this->db['mysql_write']->sql_to_array($res);



		
		$lg = explode(",", LANGUAGE_AVAILABLE);
		$nbchoice = count($lg);

        
		for ($i = 0; $i < $nbchoice; $i++)
		{
			$data['geolocalisation_country'][$i]['libelle'] = I18n::$languagesUTF8[$lg[$i]];
			$data['geolocalisation_country'][$i]['id'] = $lg[$i];
		}
		$data['default_lg'] = I18n::Get();

		$data['id_photo'] = $param[0];
		$data['nominal'] = $param[1];

		$this->set("data", $data);
	}

}