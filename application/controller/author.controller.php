<?php

class author extends controller {

	public $module_group = "Articles";

	function index() {
		
	}

	function image($param) {
		
		$this->layout_name ="admin";
		
		
		
		
		
		$_SQL = Singleton::getInstance(SQL_DRIVER);
		$sql = "SELECT * FROM species_author a
			WHERE id = '".$param[0]."'";
		
		$res = $_SQL->sql_query($sql);
		$data['author'] = $_SQL->sql_to_array($res);
		
		
		$sql = "SELECT *,a.id as id_photo,c.libelle as info_photo FROM species_picture_main a
			inner join species_tree_nominal b on a.id_species_main = b.id_nominal
			INNER JOIN species_picture_info c ON c.id = a.id_species_picture_info
			INNER JOIN species_author d ON d.id = a.id_species_author
			WHERE a.id_species_author = '".$param[0]."'
			order by id_species_main,id_species_sub, id_species_picture_info";
		
		$res = $_SQL->sql_query($sql);
		$data['photo'] = $_SQL->sql_to_array($res);
		
		
		
		//--INNER JOIN species_author d ON d.id = a.id_species_author
		$sql = "SELECT distinct a.photo_id,e.width,e.miniature,e.height,a.id as id_photo FROM species_picture_in_wait a
			inner join species_tree_nominal b on a.id_species_main = b.id_nominal
			inner join species_picture_id e ON e.photo_id = a.photo_id
			
			WHERE a.author = '".$data['author'][0]['surname']."' AND id_history_etat =1
			order by id_species_main, id_species_picture_info";
		
		$res = $_SQL->sql_query($sql);
		$data['to_valid'] = $_SQL->sql_to_array($res);

		
		
		$sql = "SELECT distinct a.photo_id,e.width,e.miniature,e.height,a.id as id_photo FROM species_picture_in_wait a
			inner join species_tree_nominal b on a.id_species_main = b.id_nominal
			inner join species_picture_id e ON e.photo_id = a.photo_id
			inner join species_picture_info f ON f.id = a.id_species_picture_info
			WHERE a.author = '".$data['author'][0]['surname']."'  AND id_history_etat =3 and f.type=3
			order by id_species_main, id_species_picture_info";
		
		$res = $_SQL->sql_query($sql);
		$data['removed'] = $_SQL->sql_to_array($res);
		
		
		
		
		
		
		
		$this->set("data", $data);
		
	}

}