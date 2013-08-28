<?php

if ( !defined('SIZE_BACKUP') )
{
	define("SIZE_BACKUP", 1024);
}

if ( !defined('SIZE_SITE_MAX') )
{
	define("SIZE_SITE_MAX", 890);
}

if ( !defined('SIZE_MINIATURE_BIG') )
{
	define("SIZE_MINIATURE_BIG", 250);
}

if ( !defined('SIZE_MINIATURE_SMALL') )
{
	define("SIZE_MINIATURE_SMALL", 158);
}

use \glial\synapse\Singleton;
use \glial\synapse\Controller;

class species extends Controller
{
	/*
	  function __contruct($a, $b, $c)
	  {
	  $this->controller = $a;
	  $this->action = $b;
	  $this->param = $c;
	  $this->view = $b;

	  parent::__construct($a, $b, $c);

	  }
	 */

	public $is_ajax = false;
	public $module_group = "Species";

	function __construct($controller, $action, $param)
	{
		parent::__construct($controller, $action, $param);



		$elem = json_decode($param, true);


		if ( !IS_AJAX )
		{

			$this->add_javascript(array('species_nominal.js', 'http://maps.googleapis.com/maps/api/js?sensor=false&language=en'));

			$this->code_javascript[] = "
		var gmap;

		function initialize(species) {
			var myOptions = {
				center: new google.maps.LatLng(0, 0),
				zoom: 1,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
			var kmzLayer = new google.maps.KmlLayer('http://www.xeno-canto.org/ranges/'+species+'.kmz');
			kmzLayer.setMap(map);
		}";

			if ( !empty($elem[1]) )
			{
				if ( $elem[1] == "general" )
				{
					$this->code_javascript[] = "
					//double only when main page, should be remove for ajax
					google.maps.event.addDomListener(window, 'load', initialize('" . $elem[0] . "'));";
				}
			}
		}
	}

	function index($param)
	{
		if ( $this->ajax($param) )
		{
			$this->layout_name = 'home';
			$_SQL = singleton::getInstance(SQL_DRIVER);


			$sql = "select * FROM species_kingdom";

			if ( $res = $_SQL->sql_query($sql) )
			{
				$ob = $_SQL->sql_fetch_object($res);

				$this->title = __("Species");
				$this->ariane = "> " . $this->title;

				//photos
				$this->data['tab_img'] = $this->get_random_photo($ob->id, "species_kingdom", 1);
				foreach ( $this->data['tab_img'] as $key => &$value )
				{
					$value['url'] = LINK . "species/kingdom/" . $value['url'];
				}
				$this->set("data", $this->data);
			}
			else
			{
				header("location: " . LINK . "species/");
				die();
			}

			$this->add_javascript(array("ext-3.0.0/adapter/ext/ext-base.js", "ext-3.0.0/ext-all.js", "api.js", "jquery-1.4.2.min.js", "jquery.history.js", "histo.js"));

			$data = array();
			$data['scientific_name'] = "Eukaryota";


			$data['node']['root'] = "n.1";
			$data['node']['name'] = $data['scientific_name'];


			if ( !IS_AJAX )
			{
				$this->code_javascript[] = $this->get_taxo_tree($data);
			}
		}
	}

	function show($param)
	{

		$data = array("gggg", "ok pas mal");

		$this->set("data", $data);
	}

	private function get_id_from_scientific_name($name, $table)
	{
		$_SQL = singleton::getInstance(SQL_DRIVER);
		$sql = "select id FROM " . $table . " WHERE scientific_name = '" . $name . "'";

		if ( $res = $_SQL->sql_query($sql) )
		{
			$ob = $_SQL->sql_fetch_object($res);
			return $ob->id;
		}
		else
		{
			die("impossible to get this scientific_name" . $name);
		}
	}

	function kingdom($param)
	{

		$this->layout_name = 'home';
		$this->ajax($param);
		$id = $this->get_id_from_scientific_name($param[0], "species_kingdom");


		$this->title = $param[0];
		$this->ariane = $this->generate_arial("kingdom", $this->title) . $this->title;

		//photos
		$this->data['tab_img'] = $this->get_random_photo($id, "species_phylum", 1);

		foreach ( $this->data['tab_img'] as $key => &$value )
		{
			$value['url'] = LINK . "species/phylum/" . $value['url'];
		}

		$sql = "CREATE VIEW species_tree_kingdom as 
			SELECT a.id as id_kingdom,
			a.scientific_name as kingdom
		FROM species_kingdom a";



		$data['scientific_name'] = $this->title;

		$data['node'] = $this->create_root_node(__FUNCTION__, $param[0]);


		$this->set("data", $this->data);


		$this->add_javascript(array("ext-3.0.0/adapter/ext/ext-base.js", "ext-3.0.0/ext-all.js", "api.js", "jquery-1.4.2.min.js", "jquery.history.js", "histo.js"));
		$this->code_javascript[] = $this->get_taxo_tree($data);
	}

	function phylum($param)
	{

		$this->layout_name = 'home';
		$this->ajax($param);

		$id = $this->get_id_from_scientific_name($param[0], "species_phylum");


		$this->title = $param[0];
		$this->ariane = $this->generate_arial("phylum", $this->title) . $this->title;

		//photos
		$data['tab_img'] = $this->get_random_photo($id, "species_class", 1);


		foreach ( $data['tab_img'] as $key => &$value )
		{
			$value['url'] = LINK . "species/classe/" . $value['url'];
		}

		$data['scientific_name'] = $this->title;
		$data['node'] = $this->create_root_node(__FUNCTION__, $param[0]);
		$this->set("data", $data);


		$sql = "CREATE VIEW species_tree_phylum as 
			SELECT a.id as id_kindomn,
			b.id as id_phylum,
			a.scientific_name as kingdom,
			b.scientific_name as phylum
		FROM species_kingdom a
		INNER JOIN species_phylum b ON a.id = b.id_species_kingdom";

		$this->add_javascript(array("ext-3.0.0/adapter/ext/ext-base.js", "ext-3.0.0/ext-all.js", "api.js", "jquery-1.4.2.min.js", "jquery.history.js", "histo.js"));
		$this->code_javascript[] = $this->get_taxo_tree($data);
	}

	function classe($param)
	{

		$this->layout_name = 'home';

		$this->ajax($param);

		$_SQL = singleton::getInstance(SQL_DRIVER);


		$sql = "select e.id as id,
		g.scientific_name as kingdom,
		f.scientific_name as phylum,
		e.scientific_name as class,
		e.scientific_name as order2, e.*
		FROM species_class e 
		INNER JOIN species_phylum f ON e.id_species_phylum = f.id
		INNER JOIN species_kingdom g ON f.id_species_kingdom = g.id
		WHERE e.scientific_name = '" . $_SQL->sql_real_escape_string($param[0]) . "'";

		if ( $res = $_SQL->sql_query($sql) )
		{
			$ob = $_SQL->sql_fetch_object($res);
			$this->data['id'] = $ob->id;
			$this->title = $param[0];
			$this->ariane = $this->generate_arial("class", $this->title) . $this->title;

			//photos
			$this->data['tab_img'] = $this->get_random_photo($ob->id, "species_order", 1);

			foreach ( $this->data['tab_img'] as $key => &$value )
			{
				$value['url'] = LINK . "species/order/" . $value['url'];
			}
		}
		else
		{
			header("location: " . LINK . "species/");
			die();
		}

		$data['scientific_name'] = $this->title;
		$data['node'] = $this->create_root_node("class", $param[0]);
		$this->set("data", $this->data);

		$sql = "CREATE VIEW species_tree_class as 
			SELECT a.id as id_kindomn,
			b.id as id_phylum,
			c.id as id_class,
			a.scientific_name as kingdom,
			b.scientific_name as phylum,
			c.scientific_name as class
		FROM species_kingdom a
		INNER JOIN species_phylum b ON a.id = b.id_species_kingdom
		INNER JOIN species_class c ON b.id = c.id_species_phylum";


		$this->add_javascript(array("ext-3.0.0/adapter/ext/ext-base.js", "ext-3.0.0/ext-all.js", "api.js", "jquery-1.4.2.min.js", "jquery.history.js", "histo.js"));
		$this->code_javascript[] = $this->get_taxo_tree($data);
	}

	function order($param)
	{

		$this->layout_name = 'home';
		$this->ajax($param);
		$_SQL = singleton::getInstance(SQL_DRIVER);


		$sql = "select d.id as id,
		g.scientific_name as kingdom,
		f.scientific_name as phylum,
		e.scientific_name as class,
		d.scientific_name as order2, d.*
		FROM species_order d 
		INNER JOIN species_class e ON d.id_species_class = e.id
		INNER JOIN species_phylum f ON e.id_species_phylum = f.id
		INNER JOIN species_kingdom g ON f.id_species_kingdom = g.id
		WHERE d.scientific_name = '" . $_SQL->sql_real_escape_string($param[0]) . "'";


		if ( $res = $_SQL->sql_query($sql) )
		{
			$ob = $_SQL->sql_fetch_object($res);


			$this->data['id'] = $ob->id;
			$this->title = $param[0];
			$this->ariane = $this->generate_arial("order2", $this->title) . $this->title;


			//photos
			$data['tab_img'] = $this->get_random_photo($ob->id, "species_family", 1);
			foreach ( $data['tab_img'] as $key => &$value )
			{
				$value['url'] = LINK . "species/family/" . $value['url'];
			}
		}
		else
		{
			header("location: " . LINK . "species/");
			die();
		}
		$data['scientific_name'] = $this->title;
		$data['node'] = $this->create_root_node(__FUNCTION__, $param[0]);

		$this->set("data", $data);

		$sql = "CREATE VIEW species_tree_order as 
			SELECT a.id as id_kindomn,
			b.id as id_phylum,
			c.id as id_class,
			d.id as id_order,
			a.scientific_name as kingdom,
			b.scientific_name as phylum,
			c.scientific_name as class,
			d.scientific_name as `order`
		FROM species_kingdom a
		INNER JOIN species_phylum b ON a.id = b.id_species_kingdom
		INNER JOIN species_class c ON b.id = c.id_species_phylum
		INNER JOIN species_order d ON c.id = d.id_species_class
		";


		$this->add_javascript(array("ext-3.0.0/adapter/ext/ext-base.js", "ext-3.0.0/ext-all.js", "api.js", "jquery-1.4.2.min.js", "jquery.history.js", "histo.js"));
		$this->code_javascript[] = $this->get_taxo_tree($data);
	}

	function family($param)
	{

		$this->layout_name = 'home';

		$this->ajax($param);

		$_SQL = singleton::getInstance(SQL_DRIVER);


		$sql = "select c.id as id,
		g.scientific_name as kingdom,
		f.scientific_name as phylum,
		e.scientific_name as class,
		d.scientific_name as order2,
		c.scientific_name as family, c.*
		FROM species_family c
		INNER JOIN species_order d ON c.id_species_order = d.id
		INNER JOIN species_class e ON d.id_species_class = e.id
		INNER JOIN species_phylum f ON e.id_species_phylum = f.id
		INNER JOIN species_kingdom g ON f.id_species_kingdom = g.id
		WHERE c.scientific_name = '" . $_SQL->sql_real_escape_string($param[0]) . "'";


		if ( $res = $_SQL->sql_query($sql) )
		{
			$ob = $_SQL->sql_fetch_object($res);

			$this->title = $param[0];
			$this->ariane = $this->generate_arial("family", $this->title) . $this->title;

			//photo_toto
			$data['tab_img'] = $this->get_random_photo($ob->id, "species_genus", 1);
			$data['id'] = $ob->id;
			foreach ( $data['tab_img'] as $key => &$value )
			{
				$value['url'] = LINK . "species/genus/" . $value['url'];
			}
		}
		else
		{
			header("location: " . LINK . "species/");
			die();
		}





		$sql = "CREATE VIEW species_tree_family as 
			SELECT a.id as id_kindomn,
			b.id as id_phylum,
			c.id as id_class,
			d.id as id_order,
			e.id as id_family,
			a.scientific_name as kingdom,
			b.scientific_name as phylum,
			c.scientific_name as class,
			d.scientific_name as `order`,
			e.scientific_name as `family`
		FROM species_kingdom a
		INNER JOIN species_phylum b ON a.id = b.id_species_kingdom
		INNER JOIN species_class c ON b.id = c.id_species_phylum
		INNER JOIN species_order d ON c.id = d.id_species_class
		INNER JOIN species_family e ON d.id = e.id_species_order
		";


		$sql = "SELECT id_nominal , nominal, count(1) as cpt,b.*  from species_tree_nominal a
		INNER JOIN species_picture_in_wait c on a.id_nominal = c.id_species_main
		INNER JOIN species_main z on z.id = a.id_nominal
		LEFT JOIN species_translation b ON a.id_nominal = b.id_row AND b.id_table = 7
		where a.id_family = " . $data['id'] . " and c.id_history_etat = 1 and z.id_history_etat = 1 
		group by a.id_nominal
		order by nominal";

		$res = $_SQL->sql_query($sql);
		$data['pending'] = $_SQL->sql_to_array($res);




		$sql = "SELECT * FROM species_tree_family_id WHERE id_species_family='" . $data['id'] . "'";
		$res = $_SQL->sql_query($sql);

		$tmp = $_SQL->sql_to_array($res);

		$data['family_name'] = $tmp[0]['scientific_name'];
		unset($tmp[0]['scientific_name']);
		$data['link'] = $tmp[0];

		$data['tree'] = implode(".", $data['link']);

		$data['scientific_name'] = $this->title;
		$data['node'] = $this->create_root_node(__FUNCTION__, $param[0]);

		//debug($data);

		$this->set("data", $data);


		if ( !IS_AJAX )
		{
			$this->add_javascript(array("ext-3.0.0/adapter/ext/ext-base.js", "ext-3.0.0/ext-all.js", "api.js", "jquery-1.4.2.min.js", "jquery.history.js", "histo.js"));
			$this->code_javascript[] = $this->get_taxo_tree($data);
		}
		//			tree.selectPath('/n.1.1.1.9.101.438/n.1.1.1.9.101." . $link->id_species_family . "." . $link->id_species_genus . "/n.1.1.1.9.101." . $link->id_species_family . "." . $link->id_species_genus . "." . $link->id_species_main . "');
	}

	function genus($param)
	{


		$this->layout_name = 'home';
		$this->ajax($param);

		$_SQL = singleton::getInstance(SQL_DRIVER);
		$sql = "select id,id_species_family,scientific_name  FROM species_genus WHERE scientific_name = '" . $_SQL->sql_real_escape_string($param[0]) . "'";



		if ( $res = $_SQL->sql_query($sql) )
		{
			$ob = $_SQL->sql_fetch_object($res);

			$this->title = $param[0];
			$this->ariane = $this->generate_arial("genus", $this->title) . $this->title;


			$this->data['id'] = $ob->id;
			$this->data['id_species_family'] = $ob->id_species_family;


			$this->data['tab_img'] = $this->get_random_photo($this->data['id'], "species_main", 1);
			//debug($this->data['tab_img']);

			foreach ( $this->data['tab_img'] as $key => &$value )
			{
				$value['url'] = LINK . "species/nominal/" . $value['url'];
			}
			$this->set("data", $this->data);
		}
		else
		{
			header("location: " . LINK . "species/");
			die();
		}





		$data['scientific_name'] = $this->title;
		$data['node'] = $this->create_root_node(__FUNCTION__, $param[0]);



		$sql = "CREATE VIEW species_tree_genus as 
			SELECT a.id as id_kindomn,
			b.id as id_phylum,
			c.id as id_class,
			d.id as id_order,
			e.id as id_family,
			f.id as id_genus,
			a.scientific_name as kingdom,
			b.scientific_name as phylum,
			c.scientific_name as class,
			d.scientific_name as `order`,
			e.scientific_name as `family`,
			f.scientific_name as `genus`
		FROM species_kingdom a
		INNER JOIN species_phylum b ON a.id = b.id_species_kingdom
		INNER JOIN species_class c ON b.id = c.id_species_phylum
		INNER JOIN species_order d ON c.id = d.id_species_class
		INNER JOIN species_family e ON d.id = e.id_species_order
		INNER JOIN species_genus f ON e.id = f.id_species_family
		";

		if ( !IS_AJAX )
		{
			$this->add_javascript(array("ext-3.0.0/adapter/ext/ext-base.js", "ext-3.0.0/ext-all.js", "api.js", "jquery-1.4.2.min.js", "jquery.history.js", "histo.js"));
			$this->code_javascript[] = $this->get_taxo_tree($data);
		}
		//			tree.selectPath('/n.1.1.1.9.101.438/n.1.1.1.9.101." . $link->id_species_family . "." . $link->id_species_genus . "/n.1.1.1.9.101." . $link->id_species_family . "." . $link->id_species_genus . "." . $link->id_species_main . "');
	}

	function test($param)
	{
		$this->layout_name = false;


		$this->title = str_replace("_", " ", $param[0]);
		echo $this->title;
	}

	function nominal($param)
	{
/*
		$this->code_javascript[] = '
			$(".selector").deleguate("click", function(){
				
			parent = $(this).parent().find("input");

			etat = $(this).parent().find("input").val();

			if (etat === 1)
			{
				$(this).removeClass( "img_valid img_invalid img_dunno" ).addClass("img_invalid");
				$(this).parent().find("input").val(0);
			}else
			{
				$(this).removeClass( "img_valid img_invalid img_dunno" ).addClass("img_valid");
				$(this).parent().find("input").val(1);
			}
		});';


*/

		$this->layout_name = 'home';


		//IS_AJAX
		//$this->ajax($param)
		if ( $this->ajax($param) )
		{

			$data['species'] = $param[0];

			$sub_species = explode("_", $param[0]);
			$data['sub_species'] = $sub_species[1];

			$_SQL = singleton::getInstance(SQL_DRIVER);

			$sql = "SELECT * FROM species_translation WHERE `scientific_name` = '" . $_SQL->sql_real_escape_string($param[0]) . "'";
			$res = $_SQL->sql_query($sql);
			$ob = $_SQL->sql_fetch_object($res);

			$data['scientific_name'] = str_replace("_", " ", $param[0]);

			$sql = "select count(1) as cpt,id_nominal from species_picture_in_wait a
			INNER JOIN species_tree_nominal b ON a.id_species_main = b.id_nominal
			WHERE id_history_etat=1 and nominal = '" . $_SQL->sql_real_escape_string($data['scientific_name']) . "'";

			$res = $_SQL->sql_query($sql);
			$ob2 = $_SQL->sql_fetch_object($res);

			$data['id_species'] = $ob2->id_nominal;
			$data['in_wait'] = $ob2->cpt;

			$this->title = $data['scientific_name'];

			if ( !empty($ob->{$GLOBALS['_LG']->Get()}) )
			{
				$this->title = $ob->{$GLOBALS['_LG']->Get()} . " (" . $this->title . ")";
			}

			$sql = "SELECT * FROM species_main a
			INNER JOIN species_taxon_author b ON a.id_species_taxon_author = b.id
			WHERE scientific_name ='" . $data['scientific_name'] . "'";
			$res = $_SQL->sql_query($sql);

			while ( $ob = $_SQL->sql_fetch_object($res) )
			{
				$this->title = $this->title . ' - ' . $ob->name;
			}

			$sql = "CREATE VIEW species_tree_nominal as 
			SELECT a.id as id_kindomn,
			b.id as id_phylum,
			c.id as id_class,
			d.id as id_order,
			e.id as id_family,
			f.id as id_genus,
			g.id as id_nominal,
			a.scientific_name as kingdom,
			b.scientific_name as phylum,
			c.scientific_name as class,
			d.scientific_name as `order`,
			e.scientific_name as `family`,
			f.scientific_name as `genus`,
			g.scientific_name as `nominal`
		FROM species_kingdom a
		INNER JOIN species_phylum b ON a.id = b.id_species_kingdom
		INNER JOIN species_class c ON b.id = c.id_species_phylum
		INNER JOIN species_order d ON c.id = d.id_species_class
		INNER JOIN species_family e ON d.id = e.id_species_order
		INNER JOIN species_genus f ON e.id = f.id_species_family
		INNER JOIN species_main g ON f.id = g.id_species_genus
		";


			$this->ariane = $this->generate_arial("species_", $data['scientific_name']) . $data['scientific_name'];
			$data['node'] = $this->create_root_node(__FUNCTION__, $data['scientific_name']);
			//ext-3.0.0/examples/direct/php/api.php => api.js


			if ( !IS_AJAX )
			{
				$this->add_javascript(array("ext-3.0.0/adapter/ext/ext-base.js", "ext-3.0.0/ext-all.js", "api.js", "jquery-1.4.2.min.js", "jquery.history.js", "histo.js", "http://maps.googleapis.com/maps/api/js?sensor=false"));
				$this->code_javascript[] = $this->get_taxo_tree($data);

				$this->code_javascript[] = '
$(".selector").live("click", function(){
	var $this = $(this),	
	$input = $this.parent().find("input"),
	etat = $input.val(),
	stateOk = etat === "1";
 
	$this.removeClass("img_valid img_invalid img_dunno").addClass(stateOk ? "img_invalid" : "img_valid");
	$input.val(stateOk ^ 1);
});
';
			}




			/*
			  $this->code_javascript[] = "
			  $(function(){
			  $('ul.menu_tab li').live('click', function(){

			  $(this).siblings().removeClass('selected');
			  $(this).addClass('selected');
			  });
			  });"; */
			$data['param'] = $param;



			$data['nb_breeder'] = 0;
			$sql2 = "SELECT count(1) as cpt FROM link__species_sub__user_main 
				WHERE `id_species_main` = '" . $_SQL->sql_real_escape_string($data['id_species']) . "'
				GROUP BY id_user_main";
			$res2 = $_SQL->sql_query($sql2);
			while ( $ob = $_SQL->sql_fetch_object($res2) )
			{
				$data['nb_breeder'] = $ob->cpt;
			}

			$data['pending'] = 0;

			$sql3 = "SELECT count(distinct b.id_species_picture_id) as cpt
				FROM species_picture_search a
				inner join link__species_picture_id__species_picture_search b ON b.id_species_picture_search = a.id
				WHERE a.id_species_main = " . $_SQL->sql_real_escape_string($data['id_species']) . "
				";


			$res3 = $_SQL->sql_query($sql3);
			while ( $ob3 = $_SQL->sql_fetch_object($res3) )
			{
				$data['pending'] = $ob3->cpt;
			}



			$this->set("data", $data);
		}
	}

	function sub_species($param)
	{

		$this->layout_name = 'home';
		//debug($param);
		//echo "hxhfg";
		//$this->ajax($param);

		$_SQL = singleton::getInstance(SQL_DRIVER);

		$data['param'] = $param;
		$data['link'] = $param[0];
		$data['scientific_name'] = $_SQL->sql_real_escape_string(str_replace("_", " ", $param[0]));

		$tab = explode(" ", $data['scientific_name']);
		$data['sub_species'] = $data['scientific_name'] . " " . $tab[1];


		$data['sub_species_link'] = str_replace(" ", "_", $data['sub_species']);

		$sql = "select a.*,a.id as id_subspeceis, b.id as id_species, c.*
		from species_sub a
		INNER JOIN species_main b ON a.id_species_main	= b.id
		INNER JOIN species_tree_nominal c ON a.id_species_main = c.id_nominal
		where b.scientific_name='" . $data['scientific_name'] . "'
		ORDER BY a.scientific_name asc";

		$res = $_SQL->sql_query($sql);

		$data['sub'] = $_SQL->sql_to_array($res);


		$i = 0;
		foreach ( $data['sub'] as &$line )
		{
			if ( $line['id_species_picture_main'] == 0 )
			{
				$sql = "SELECT * FROM species_picture_main
					WHERE id_species_main ='" . $line['id_species'] . "'
					AND id_species_sub ='" . $line['id_subspeceis'] . "'
					AND id_history_etat ='1'";

				$res = $_SQL->sql_query($sql);

				while ( $ob = $_SQL->sql_fetch_object($res) )
				{
					$line['id_species_picture_main'] = $ob->id;
					break;
				}
			}
			$line['sub_species_link'] = str_replace(" ", "_", $line['scientific_name']);

			$data['tab_img'][$i]['url'] = "#" . LINK . "species/distribution_description/" . $line['sub_species_link'] . "/distribution_description>result_2";
			$data['tab_img'][$i]['name'] = $line['scientific_name'];

			if ( empty($line['id_species_picture_main']) )
			{
				$data['tab_img'][$i]['photo'][0] = IMG . "main/nopictureavailable.png";
			}
			else
			{
				$data['tab_img'][$i]['photo'][0] = FARM1 . "crop/" . SIZE_MINIATURE_SMALL . "x"
					. SIZE_MINIATURE_SMALL . "/Eukaryota/" . $line['kingdom'] . "/" . $line['phylum'] . "/" . $line['class'] . "/" . $line['order'] . "/"
					. $line['family'] . "/" . $line['genus'] . "/" . $param[0] . '/' . $line['id_species_picture_main'] . '-' . $param[0] . ".jpg";
			}

			$i++;
		}


		$this->set("data", $data);
	}

	function distribution_description($param)
	{
		//debug($param);


		$this->layout_name = false;

		$_SQL = singleton::getInstance(SQL_DRIVER);

		$data['link'] = $param[0];
		$data['scientific_name'] = str_replace("_", " ", $param[0]);


		$sql = "SELECT * FROM species_sub WHERE scientific_name = '" . $data['scientific_name'] . "'";
		$res = $_SQL->sql_query($sql);
		$data['subspecies'] = $_SQL->sql_to_array($res);


		$sql = "SELECT * FROM species_picture_main a
			INNER JOIN species_tree_nominal b ON b.id_nominal = a.id_species_main
			WHERE id_species_sub ='" . $data['subspecies'][0]['id'] . "'
			AND id_history_etat =1";
		$res = $_SQL->sql_query($sql);
		$data['tab_img'] = $_SQL->sql_to_array($res);

		foreach ( $data['tab_img'] as &$line )
		{

			$line['link_nominal'] = str_replace(" ", "_", $line['nominal']);

			$line['url'] = "";
			$line['photo'] = FARM1 . "crop/" . SIZE_MINIATURE_BIG . "x"
				. SIZE_MINIATURE_BIG . "/Eukaryota/" . $line['kingdom'] . "/" . $line['phylum'] . "/" . $line['class'] . "/" . $line['order'] . "/"
				. $line['family'] . "/" . $line['genus'] . "/" . $line['link_nominal'] . '/' . $line['id'] . '-' . $line['link_nominal'] . ".jpg";

			$line['name'] = $data['scientific_name'];
		}
		/*
		 * http://www.estrildidae.net/image/crop/250x250/Eukaryota/Animalia/Chordata/Aves/Passeriformes/Estrildidae/Lonchura/Lonchura_atricapilla/10098-Lonchura_atricapilla.jpg
		  <img width="250" height="250" alt="73457708_a1b407f122_o.jpg" title="73457708_a1b407f122_o.jpg"
		  src="http://www.estrildidae.net/image/crop/158x158/Eukaryota/Animalia/Chordata/Aves/Passeriformes/Estrildidae/Lonchura/Lonchura_atricapilla_formosana/10098-Lonchura_atricapilla_formosana.jpg">
		  //debug($data);
		 */

		$this->set("data", $data);
	}

	function general($param)
	{
		$this->layout = false;


		$_SQL = singleton::getInstance(SQL_DRIVER);

		$sql = "select a.id as id,
		a.id_species_iucn,
		a.scientific_name,
		c.libelle,c.code_iucn,
		a.id as id_species,
		e.family
		FROM species_main a
		INNER JOIN species_tree_nominal e ON e.id_nominal = a.id
		LEFT JOIN species_iucn c ON c.Id = a.id_species_iucn
		WHERE a.scientific_name = '" . $_SQL->sql_real_escape_string(str_replace("_", " ", $param[0])) . "'";

		$res = $_SQL->sql_query($sql);

		//echo $sql;
		$data['info'] = $_SQL->sql_to_array($res);




		$sql = "SELECT a.*,b.print_name
			FROM scientific_name_translation a
			INNER JOIN language b ON a.language = b.iso3
			WHERE id_species_main = " . $data['info'][0]['id'] . " and id_species_sub = 0
			ORDER BY print_name, is_valid DESC, text";
		$res = $_SQL->sql_query($sql);
		$data['translation'] = $_SQL->sql_to_array($res);


		$sql = "select b.url, b.name,b.pic16, a.*
		FROM  species_source_detail a
		INNER JOIN species_source_main b ON a.id_species_source_main = b.id
		WHERE a.id_species_main = '" . $_SQL->sql_real_escape_string($data['info'][0]['id_species']) . "'
			AND a.id_species_sub = 0";

		$res = $_SQL->sql_query($sql);
		$data['source'] = $_SQL->sql_to_array($res);



		$sql = "select *
		FROM  species_iucn
		WHERE is_valid=1
		ORDER BY cf_order DESC";
		$res = $_SQL->sql_query($sql);
		$data['iucn'] = $_SQL->sql_to_array($res);



		$sql = "SELECT id_geolocalisation_country,b.libelle,b.iso,c.libelle as distribution FROM link__geolocalisation_country__species_main a
			INNER JOIN geolocalisation_country b ON a.id_geolocalisation_country = b.id
			INNER JOIN species_distribution_information c ON c.id = a.id_species_distribution_information
			WHERE a.id_species_main = '" . $_SQL->sql_real_escape_string($data['info'][0]['id_species']) . "'
			ORDER by c.libelle, b.libelle";
		$res = $_SQL->sql_query($sql);
		$data['geographic_range'] = $_SQL->sql_to_array($res);


		$sql = "select * FROM species_tree_nominal WHERE id_nominal = '" . $_SQL->sql_real_escape_string($data['info'][0]['id_species']) . "'";
		$res = $_SQL->sql_query($sql);
		$data['species'] = $_SQL->sql_to_array($res);


		$sql = "SELECT id_species_picture_main as id FROM link__species_main__species_picture_main a
			WHERE a.id_species_main = '" . $_SQL->sql_real_escape_string($data['info'][0]['id_species']) . "'
			ORDER BY a.rank asc";
		//--INNER JOIN species_picture_main b ON a.id_species_picture_main  = b.id
		$res = $_SQL->sql_query($sql);
		$data['photo'] = $_SQL->sql_to_array($res);



		//$data['is_ajax'] = $this->is_ajax;

		$this->set('data', $data);
	}

	function photo($param)
	{
		$this->layout = false;

		$_SQL = Singleton::getInstance(SQL_DRIVER);

		$data = array();



		if ( $param[2] == "photo_detail" )
		{
			$this->photo_detail($param);
			return;
		}
		else
		{


			$sql = "SELECT a.id as id_photo,c.libelle as info_photo,d.scientific_name,b.nominal,b.*
			FROM species_picture_main a
			inner join species_tree_nominal b on a.id_species_main = b.id_nominal
			INNER JOIN species_picture_info c ON c.id = a.id_species_picture_info
			LEFT JOIN species_sub d ON d.id = a.id_species_sub
			WHERE b.nominal = '" . $_SQL->sql_real_escape_string(str_replace("_", " ", $param[0])) . "'
				AND c.type =1
			order by id_species_sub, id_species_picture_info";

			$res = $_SQL->sql_query($sql);
			$data['photo'] = $_SQL->sql_to_array($res);
		}





		$this->set('data', $data);
	}

	function photo_detail($param)
	{
		$this->layout = false;


		$this->view = 'photo_detail';




		$_SQL = singleton::getInstance(SQL_DRIVER);
		$sql = "SELECT *,a.id as id_photo,c.libelle as info_photo, height, width FROM species_picture_main a
			inner join species_tree_nominal b on a.id_species_main = b.id_nominal
			INNER JOIN species_picture_info c ON c.id = a.id_species_picture_info
			INNER JOIN species_author d ON d.id = a.id_species_author
			WHERE b.nominal = '" . $_SQL->sql_real_escape_string(str_replace("_", " ", $param[0])) . "'
				AND a.id = '" . $_SQL->sql_real_escape_string($param[3]) . "'
				AND c.type =1
			order by id_species_sub, id_species_picture_info";

		$res = $_SQL->sql_query($sql);
		$data['photo'] = $_SQL->sql_to_array($res);



		$this->set('data', $data);
	}

	function block_video()
	{
		
	}

	function block_last_photo()
	{

		include_once LIB . 'imageprocessor.lib.php';

		$this->javascript = array("jquery-1.4.4.min.js", "jawdropper_slider.js");
		$this->code_javascript[] = "
		$(document).ready(function() {
			$('#slider').jdSlider({ 
				showSelectors : false,
				showNavigation     : false,
				showCaption        : true,
				width  : 250,
				height : 250,
				transitions : 'stretchOut, lightBeam, fade, sliceFade, lightBeam,shrink, sliceFade,slide, slideOver',
				randomTransitions : true,
				pauseOnHover       : true
			});
		});";
		$_SQL = singleton::getInstance(SQL_DRIVER);


		$sql = "select *,a.id as id_photo
		FROM species_picture_main a
		INNER JOIN species_tree_name b ON a.id_species_main = b.Id
		INNER JOIN species_translation z ON z.id_row = a.id_species_main and id_table = 7
		WHERE a.id_history_etat = 1
		ORDER BY date_validated DESC LIMIT 8";
		$res = $_SQL->sql_query($sql);

		$data = $_SQL->sql_to_array($res);

		$this->set("data", $data);

		//debug($data);
	}

	function router()
	{

		function doRpc($cdata)
		{
			global $API;
			try
			{
				$API = array('TestAction' => array(
						'methods' => array(
							'doEcho' => array(
								'len' => 1
							),
							'getTree' => array(
								'len' => 1
							)
						)
					)
				);

				if ( !isset($API[$cdata->action]) )
				{
					throw new Exception('Call to undefined action: ' . $cdata->action);
				}

				$action = $cdata->action;
				$a = $API[$action];

				doAroundCalls($a['before'], $cdata);

				$method = $cdata->method;
				$mdef = $a['methods'][$method];
				if ( !$mdef )
				{
					throw new Exception("Call to undefined method: $method on action $action");
				}
				doAroundCalls($mdef['before'], $cdata);

				$r = array(
					'type' => 'rpc',
					'tid' => $cdata->tid,
					'action' => $action,
					'method' => $method
				);

				//require_once("classes/$action.php");
				$o = new $action();

				$params = isset($cdata->data) && is_array($cdata->data) ? $cdata->data : array();

				$r['result'] = call_user_func_array(array($o, $method), $params);

				doAroundCalls($mdef['after'], $cdata, $r);
				doAroundCalls($a['after'], $cdata, $r);
			}
			catch ( Exception $e )
			{
				$r['type'] = 'exception';
				$r['message'] = $e->getMessage();
				$r['where'] = $e->getTraceAsString();
			}
			return $r;
		}

		function doAroundCalls(&$fns, &$cdata, &$returnData = null)
		{
			if ( !$fns )
			{
				return;
			}
			if ( is_array($fns) )
			{
				foreach ( $fns as $f )
				{
					$f($cdata, $returnData);
				}
			}
			else
			{
				$fns($cdata, $returnData);
			}
		}

		$this->layout_name = false;


		$isForm = false;
		$isUpload = false;


		if ( isset($GLOBALS['HTTP_RAW_POST_DATA']) )
		{
			$postdata = file_get_contents("php://input");


			//die($postdata);
		}

		//$postdata = '{"action":"TestAction","method":"getTree","data":["n.1.1.1.9.101.438"],"type":"rpc","tid":2}';

		if ( isset($postdata) )
		{
			header('Content-Type: text/javascript');
			$data = json_decode($postdata);
		}
		else
		{
			die('Invalid request.');
		}

		$response = null;
		if ( is_array($data) )
		{
			$response = array();
			foreach ( $data as $d )
			{
				$response[] = doRpc($d);
			}
		}
		else
		{
			$response = doRpc($data);
		}

		if ( $isForm && $isUpload )
		{
			echo '<html><body><textarea>';
			echo json_encode($response);
			echo '</textarea></body></html>';
		}
		else
		{
			echo json_encode($response);
		}

		exit;
	}

	function get_species_name_by_scientific_name($name, $lg)
	{
		$_SQL = singleton::getInstance(SQL_DRIVER);


		$sql = "select * from SpeciesTranslation where IdRow = '" . $_SQL->sql_real_escape_string($id) . "' and IdTable =7";
		$res = $_SQL->sql_query($sql);

		if ( $_SQL->sql_num_rows($res) == 1 )
		{
			$ob = $_SQL->sql_fetch_object($res);

			//echo "language : ".$ob->$lg;

			if ( @$ob->$lg != "" )
				return $ob->$lg;
			else
				return str_replace("_", " ", $ob->ScientificName);
		}
		else
		{
			return "Undefined";
		}
	}

	private function get_random_photo($id, $table, $number = 1)
	{
		$_SQL = singleton::getInstance(SQL_DRIVER);


		switch ( $table )
		{
			case "species_kingdom": $id_mother = $id;
				break;
			case "species_phylum": $id_mother = "id_species_kingdom";
				break;
			case "species_class": $id_mother = "id_species_phylum";
				break;
			case "species_order": $id_mother = "id_species_class";
				break;
			case "species_family": $id_mother = "id_species_order";
				break;
			case "species_genus": $id_mother = "id_species_family";
				break;
			case "species_main": $id_mother = "id_species_genus";
				break;
			case "species_sub": $id_mother = "id_species_main";
				break;
		}

		$sql2 = "SELECT * FROM " . $table . " WHERE " . $id_mother . " = " . $id . " AND id_history_etat = 1 ORDER BY scientific_name";

		$res2 = $_SQL->sql_query($sql2);
		$i = 0;

		while ( $ob2 = $_SQL->sql_fetch_object($res2) )
		{
			$data[$i]['name'] = $ob2->scientific_name;

			if ( $id_mother == "id_species_genus" )
			{
				$data[$i]['url'] = str_replace(' ', '_', $ob2->scientific_name) . "/general/";
			}
			else
			{
				$data[$i]['url'] = str_replace(' ', '_', $ob2->scientific_name) . "/";
			}



			$sql = "select count(1) as cpt FROM species_picture_main a
			INNER JOIN  species_tree_id b ON a.id_species_main = b.id_species_main
			WHERE b.id_" . $table . " = " . $ob2->id . "
				AND id_history_etat = 1";

			$res3 = $_SQL->sql_query($sql);

			$ob = $_SQL->sql_fetch_object($res3);

			$j = 0;

			if ( $ob->cpt > 0 )
			{

				$rand = mt_rand(0, $ob->cpt - 1);

				$sql = "select *, a.id as id_ptoho FROM species_picture_main a
				INNER JOIN species_tree_id b ON a.id_species_main = b.id_species_main
				INNER JOIN species_tree_name c ON c.id = b.id_species_main
				WHERE b.id_" . $table . " = " . $ob2->id . " 
				AND id_history_etat = 1	
				LIMIT " . $rand . ", " . $number;
				$res4 = $_SQL->sql_query($sql);

				while ( $ob4 = $_SQL->sql_fetch_object($res4) )
				{

					$species_name = str_replace(" ", "_", $ob4->species_);
					$path = "Eukaryota/{$ob4->kingdom}/{$ob4->phylum}/{$ob4->class}/{$ob4->order2}/{$ob4->family}/{$ob4->genus}/" . $species_name;
					$picture_name = $ob4->id_ptoho . "-" . $species_name . ".jpg";
					$data[$i]['photo'][] = FARM1 . "crop/" . SIZE_MINIATURE_SMALL . "x" . SIZE_MINIATURE_SMALL . "/" . $path . DS . $picture_name;
					$data[$i]['photo2'][] = FARM1 . "crop/" . SIZE_MINIATURE_BIG . "x" . SIZE_MINIATURE_BIG . "/" . $path . DS . $picture_name;
					$j++;
				}
			}

			for ( $k = $j; $k < $number; $k++ )
			{
				$data[$i]['photo'][] = IMG . "main/nopictureavailable.png";
			}

			$i++;
		}

		return $data;
	}

	private function generate_arial($table, $id)
	{
		$_SQL = singleton::getInstance(SQL_DRIVER);



		$sql = "SELECT * FROM species_tree_name WHERE " . $table . " = '" . $_SQL->sql_real_escape_string($id) . "' LIMIT 1";

		//debug($sql);

		$res = $_SQL->sql_query($sql);
		$tmp = $_SQL->sql_to_array($res);

		$this->data['species'] = $tmp[0];

		switch ( $table )
		{
			case 'kingdom': $lgt = 1;
				break;
			case 'phylum': $lgt = 2;
				break;
			case 'class': $lgt = 3;
				break;
			case 'order2': $lgt = 4;
				break;
			case 'family': $lgt = 5;
				break;
			case 'genus': $lgt = 6;
				break;
			case 'species_': $lgt = 7;
				break;
			default :
				die("unknow table : " . $table);
				break;
		}

		$arial = "> ";
		$arial .= "<a href=\"" . LINK . "species/\">" . __("Species") . "</a> > ";

		if ( $lgt > 1 )
		{
			$arial .= "<a href=\"" . LINK . "species/kingdom/" . $this->data['species']['kingdom'] . "\">" . $this->data['species']['kingdom'] . "</a> > ";

			if ( $lgt > 2 )
			{
				$arial .= "<a href=\"" . LINK . "species/phylum/" . $this->data['species']['phylum'] . "\">" . $this->data['species']['phylum'] . "</a> > ";

				if ( $lgt > 3 )
				{
					$arial .= "<a href=\"" . LINK . "species/classe/" . $this->data['species']['class'] . "\">" . $this->data['species']['class'] . "</a> > ";

					if ( $lgt > 4 )
					{
						$arial .= "<a href=\"" . LINK . "species/order/" . $this->data['species']['order2'] . "\">" . $this->data['species']['order2'] . "</a> > ";

						if ( $lgt > 5 )
						{
							$arial .= "<a href=\"" . LINK . "species/family/" . $this->data['species']['family'] . "\">" . $this->data['species']['family'] . "</a> > ";

							if ( $lgt > 6 )
							{
								$arial .= "<a href=\"" . LINK . "species/genus/" . $this->data['species']['genus'] . "\">" . $this->data['species']['genus'] . "</a> > ";

								if ( $lgt > 7 )
								{
									$arial .= "<a href=\"" . LINK . "species/genus/" . $this->data['species']['species_'] . "\">" . $this->data['species']['species_'] . "</a> > ";
								}
							}
						}
					}
				}
			}
		}

		return $arial;
	}

	private function ajax($param)
	{

		//debug($param);


		foreach ( $param as $line )
		{
			if ( strstr($line, '>') )
			{
				$this->is_ajax = true;

				$exploded = explode('>', $line);


				$id_source = $exploded[0];
				$target = $exploded[1];


				switch ( $id_source )
				{
					case "target":
						$this->layout_name = "page";
						return true;
						break;

					default:

						$this->layout_name = false;

						$this->$param[1]($param);
						$this->layout_name = false;
						$this->view = $param[1];

						return false;
						break;
				}
				break;
			}
		}

		return true;
	}

	private function get_taxo_tree($data)
	{




		$js = "
		Ext.onReady
		(
			function()
			{
				Ext.Direct.addProvider(Ext.app.REMOTING_API);
				var tree = new Ext.tree.TreePanel
				(
					{
						autoScroll: true,
						renderTo: document.getElementById('menu_cadre_arbre'),
						animate: false, //lol
						rootVisible:true,
						enableDD:false,  //drag and drop
						
						root: {
							expanded:true,
							useArrows: false,
							id: '" . $data['node']['root'] . "', //n.1.1.1.9.101.438   n.1.1.1.9.104.270
							text: '" . $data['node']['name'] . "',
							
						},
						preloadChilren: true,
						
						listeners:
						{
							click: function(n)
							{
								var a,b,c,name;
								a = n.attributes.id.split('.');
								
								if (a.length == 2)
								{
									b = a[a.length-1];
									var  reg=new  RegExp('( )', 'g');
									window.location = '#" . LINK . "species/index/target>page';
								}

								if (a.length > 2 && a.length < 9)
								{
									switch (a.length)
									{
										case 3: name = 'kingdom'; break;
										case 4: name = 'phylum'; break;
										case 5: name = 'classe'; break;
										case 6: name = 'order'; break;
										case 7: name = 'family'; break;
										case 8: name = 'genus'; break;
									}
									
	
									var target = 'page';
									var page2 = '" . LINK . "species/'+name+'/'+n.attributes.text+'/';
									var url_ajax = page2+'target>'+target+'/';
									

									$('#page').hide(0, function(){
										$(this).load(url_ajax, 'data', function(){
											$(this).fadeIn(200);

											var data = $(this).html();

											
											var id_a = 'ff3333';
											var etat = {data: data, id_a: id_a, url_ajax: url_ajax,type_link: 'species',target :target};
											history.pushState(etat, document.title, page2);
										});
									});
								}

								if (a.length == 9)
								{
									b = a[a.length-1];
									var  reg=new  RegExp('( )', 'g');
									
									

									var target = 'page';
									var page2 = '" . LINK . "species/nominal/'+n.attributes.text.replace(reg,'_')+'/general/';
									var url_ajax = page2+'target>'+target+'/';
									

									$('#page').hide(0, function(){
										$(this).load(url_ajax, 'data', function(){
											$(this).fadeIn(200);

											var data = $(this).html();

											
											var id_a = 'ff3333';
											var etat = {data: data, id_a: id_a, url_ajax: url_ajax,type_link: 'species',target :target};
											history.pushState(etat, document.title, page2);
										});
									});

								}
							}
						},

						loader: 
						new Ext.tree.TreeLoader
						(
							{
								directFn: TestAction.getTree,
							}
						),
					}
				);
				";


		//window.location = '#" . LINK . "species/'+name+'/'+n.attributes.text+'/target>page';

		if ( !empty($data['node']['selected_tree']) )
		{
			$js .= "tree.selectPath('" . $data['node']['selected_tree'] . "');";
			$js .= "tree.expandPath('" . $data['node']['selected_tree'] . "');";
		}


		$js .= "return true;

			}
		);";



		return $js;
	}

	private function create_root_node($taxon, $value)
	{

		$data = array();
		$_SQL = singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT * FROM `species_tree_" . $taxon . "` WHERE `" . $taxon . "` = '" . $_SQL->sql_real_escape_string($value) . "'";

		$res = $_SQL->sql_query($sql);
		$tab = $_SQL->sql_to_array($res);


		$root = "n.1";
		$node = "n.1";
		$tree = "n.1";


		foreach ( $tab[0] as $key => $value )
		{

			if ( strstr($key, 'id_') )
			{
				$node = $node . "." . $value;

				$tree = $tree . '/' . $node;


				if ( $key === "id_genus" )
				{
					continue;
				}
				if ( $key === "id_nominal" )
				{
					continue;
				}

				$root = $root . "." . $value;
			}
			else
			{

				if ( $key === "nominal" )
				{
					$path = true;
					continue;
				}
				if ( $key === "genus" )
				{
					$path = true;
					continue;
				}

				$name = $value;
			}
		}

		if ( !empty($path) )
		{
			$select_tree = strstr($tree, "/" . $root);

			if ( $select_tree )
			{
				$data['selected_tree'] = $select_tree;
			}
		}
		else
		{
			$data['selected_tree'] = false;
		}

		$data['root'] = $root;
		$data['tree'] = $tree;
		$data['name'] = $name;
		$data['id'] = $tab[0]['id_' . $taxon];

		//debug($data);
		return $data;
	}

	function maping_latinname_distribution()
	{
		$_SQL = singleton::getInstance(SQL_DRIVER);
		$sql = "SELECT id_species,species,fr FROM species_tree_species a
			INNER JOIN species_translation b ON a.id_species = b.id_row AND id_table = 7
			WHERE a.class = 'Aves'";

		$res = $_SQL->sql_query($sql);

		while ( $ob = $_SQL->sql_fetch_object($res) )
		{
			$str = $ob->fr;

			$str = strtr($str, 'ÁÀÂÄÃÅÇÉÈÊËÍÏÎÌÑÓÒÔÖÕÚÙÛÜÝ', 'AAAAAACEEEEEIIIINOOOOOUUUUY');
			$str = strtr($str, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ', 'aaaaaaceeeeiiiinooooouuuuyy');

			$str = str_replace(' ', '.', $str);
			$str = str_replace('\'', '.', $str);

			if ( file_exists('Y:\\data\\extraction\\www.oiseaux.net\\distribution\\img1\\' . $str . '.png') )
			{
				
			}
		}
	}

	function insert_distribution_iucn()
	{

		include_once(LIBRARY . "Glial/species/species.php");

		function gzdecode($data)
		{
			return gzinflate(substr($data, 10, -8));
		}

		$_SQL = singleton::getInstance(SQL_DRIVER);
		$sql = "SELECT id_species_main,data FROM species_source_detail a
			INNER JOIN species_source_data b ON a.id = b.id_species_source_detail
			WHERE type='summary'";

		//order by b.id desc LIMIT 1000";


		$res = $_SQL->sql_query($sql);




		$cp = array();

		while ( $ob = $_SQL->sql_fetch_object($res) )
		{

			$data = json_decode(gzdecode(base64_decode($ob->data)), true);

			//debug($data);


			echo "-------------------------------------------\n";
			if ( !empty($data['Geographic']['Countries']) )
			{



				foreach ( $data['Geographic']['Countries'] as $type => $array_country )
				{
					$id_species_distribution_information = species_tree::get_id_species_distribution_information($type);

					foreach ( $array_country as $country )
					{

						$out = explode(" ", $country);
						$out[0] = str_replace(",", "", $out[0]);

						switch ( $out[0] )
						{
							case "Brazil":
								$country = $out[0];
								break;
							case "Venezuela":
								$country = $out[0];
								break;
							case "Malaysia":
								$country = $out[0];
								break;
							case "Spain":
								$country = $out[0];
								break;

							case "Italy":
								$country = $out[0];
								break;
							case "Canada":
								$country = $out[0];
								break;

							case "China":
								$country = $out[0];
								break;
							case "India":
								$country = $out[0];
								break;
							case "Greece":
								$country = $out[0];
								break;
							case "Mexico":
								$country = $out[0];
								break;
							case "Japan":
								$country = $out[0];
								break;
							case "Taiwan":
								$country = $out[0];
								break;
							case "Russian":
								$country = "Russian Federation";
								break;
							case "Armenia":
								$country = $out[0];
								break;

							case "Azerbaijan":
								$country = $out[0];
								break;

							case "Indonesia":
								$country = $out[0];
								break;

							case "Georgia":
								$country = $out[0];
								break;

							case "Bolivia":
								$country = $out[0];
								break;

							case "Guernsey":
							case "Jersey":
								$country = "United Kingdom";
								break;
						}

						if ( !empty($out[1]) )
						{
							$out[1] = str_replace(",", "", $out[1]);

							switch ( $out[1] )
							{
								case "States":
									$country = "United States";
									break;

								case "Barthélemy":
								case "Martin":
									$country = "France";
									break;

								case "Helena":
									$country = "Saint Helena";
									break;
							}
						}


						switch ( $country )
						{
							case "Isle of Man":
								$country = "United Kingdom";
								break;
							case "Åland Islands":
								$country = "Sweden";
								break;
						}



						$delete_param = stristr($country, '(', true);
						if ( $delete_param )
						{
							$country = trim($delete_param);
						}

						$sql = "SELECT * FROM geolocalisation_country where libelle = '" . $_SQL->sql_real_escape_string($country) . "' OR alias = '" . $_SQL->sql_real_escape_string($country) . "'";
						$res2 = $_SQL->sql_query($sql);

						if ( $_SQL->sql_num_rows($res2) == 0 )
						{
							echo "ERROR\n";
							echo $sql . "\n";
							debug($_SQL->sql_error());
							debug($country);
							//die();

							$cp[] = $country;
						}
						elseif ( $_SQL->sql_num_rows($res2) == 1 )
						{
							$ob2 = $_SQL->sql_fetch_object($res2);
							species_tree::add_country_to_species($ob->id_species_main, $ob2->id, $id_species_distribution_information);
						}
						else
						{
							while ( $ob2 = $_SQL->sql_fetch_array($res2) )
							{
								debug($ob2);
							}

							die('UNKNOW ERROR !!!');
						}

						echo $type . " " . $country . "\n";
					}
				}
			}
		}

		$cp = array_unique($cp);
		debug($cp);

		exit;
	}

	function country($param)
	{
		$this->layout_name = 'home';

		$_SQL = singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT * FROM geolocalisation_country WHERE iso ='" . $_SQL->sql_real_escape_string($param[0]) . "'";
		$res = $_SQL->sql_query($sql);
		$ob = $_SQL->sql_fetch_object($res);

		$this->title = __($ob->libelle);

		$this->ariane = $this->generate_arial("family", $param[1])
			. ' <a href="' . LINK . 'species/family/' . $param[1] . '/">' . $param[1] . '</a> > '
			. '<img class="country" src="' . IMG . '/country/type2/' . strtolower($ob->iso) . '.png" width="16" height="11"> ' . $this->title;



		$sql = "SELECT e.libelle,scientific_name,d.*,f.id_species_picture_main
			
			FROM link__geolocalisation_country__species_main a
			INNER JOIN species_main b ON a.id_species_main = b.id
			left JOIN link__species_main__species_picture_main f on f.id_species_main = b.id
			INNER JOIN species_tree_nominal d ON b.id = d.id_nominal
			INNER JOIN species_distribution_information e ON e.id = a.id_species_distribution_information
			INNER JOIN geolocalisation_country c ON c.id = a.id_geolocalisation_country
			WHERE d.family='" . $_SQL->sql_real_escape_string($param[1]) . "' and c.iso ='" . $_SQL->sql_real_escape_string($param[0]) . "'
				ORDER BY e.id, scientific_name";

		$res = $_SQL->sql_query($sql);

		$data['species'] = $_SQL->sql_to_array($res);



		$this->set("data", $data);
	}

	function block_country($param)
	{


		$_SQL = singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT a.id_geolocalisation_country,c.iso,c.libelle,count(1) as cpt,d.name FROM link__geolocalisation_country__species_main a
			INNER JOIN species_tree_nominal b ON a.id_species_main = b.id_nominal
			inner join geolocalisation_country c ON c.id = a.id_geolocalisation_country
			inner join geolocalisation_continent d ON d.id = c.id_geolocalisation_continent
			WHERE family = '" . $_SQL->sql_real_escape_string($param[1]) . "'
				GROUP BY a.id_geolocalisation_country
				ORDER BY d.name,libelle";
		$res = $_SQL->sql_query($sql);

		$data['country'] = $_SQL->sql_to_array($res);
		$data['family'] = $param[1];
		$data['iso'] = $param[0];
		$this->set("data", $data);
	}

	function one_shoot_update_lang()
	{
		$_SQL = singleton::getInstance(SQL_DRIVER);
		$sql = "SELECT * FROM species_translation";

		$res = $_SQL->sql_query($sql);


		$i = 0;
		$lg = array("fr", "en", "de", "es", "nl", "it", "ja", "cs", "pl", "zh-cn", "ru", "fi", "pt", "da", "no", "sk", "se", "is", "ta");
		while ( $tab = $_SQL->sql_fetch_array($res, MYSQL_ASSOC) )
		{

			$i++;
			foreach ( $tab as $key => $elem )
			{
				if ( in_array($key, $lg) )
				{
					if ( !empty($elem) )
					{
						$data = array();
						$data['scientific_name_translation']['id_species_main'] = $tab['id_row'];
						$data['scientific_name_translation']['language'] = $key;
						$data['scientific_name_translation']['text'] = $_SQL->sql_real_escape_string($elem);
						$data['scientific_name_translation']['is_valid'] = 1;

						echo $i . " [" . date("Y-m-d H:i:s") . "] " . $elem . "\n";

						if ( !$_SQL->sql_save($data) )
						{
							debug($data);
							debug($_SQL->sql_error());
							die();
						}
					}
				}
			}
		}
	}

	private function get_media($id_species)
	{
		$_SQL = singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT latitude,longitude FROM species_picture_main where id_species_main = '" . $id_species . "' and latitude != 0";

		$res = $_SQL->sql_query($sql);

		$data = $_SQL->sql_to_array($res);

		return $data;
	}

	function get_species_id_by_scientific()
	{

		$this->layout_name = false;
		$this->view = false;

		$_SQL = singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT scientific_name, id FROM species_main WHERE scientific_name LIKE '" . $_SQL->sql_real_escape_string($_GET['q']) . "%'
		ORDER BY scientific_name LIMIT 0,100";
		$res = $_SQL->sql_query($sql);
		$data = $_SQL->sql_to_array($res);



		foreach ( $data as $line )
		{
			echo $line['scientific_name'] . "|" . $line['id'] . "\n";
		}
	}

	function xc()
	{
		$this->view = false;
		$this->layout_name = false;

		include_once(LIBRARY . "Glial/parser/xeno_canto/xeno_canto.php");
		include_once (LIB . "wlHtmlDom.php");

		$_SQL = singleton::getInstance(SQL_DRIVER);

		xeno_canto::get_kmz();
	}

	function import_photo()
	{
		$this->view = false;
		$this->layout_name = false;

		$_SQL = singleton::getInstance(SQL_DRIVER);

		$sql = "INSERT INTO link__species_main__species_picture_main (id_species_picture_main, id_species_main) SELECT min(id) as id , id_species_main FROM species_picture_main 
		where id_species_main not in (select distinct id_species_main from link__species_main__species_picture_main) and id_history_etat=1
		group by id_species_main";

		$_SQL->sql_query($sql);
	}

	function breeder($param)
	{
		$this->layout = false;

		$_SQL = singleton::getInstance(SQL_DRIVER);


		$scientific_name = str_replace('_', ' ', $param[0]);

		$sql = "SELECT *, (a.male+a.female+a.unknow) AS SUM_COUNTS,
			e.libelle as country,
			f.libelle as city
			
			FROM link__species_sub__user_main a
			INNER JOIN user_main b ON a.id_user_main = b.id
			INNER JOIN species_main d ON a.id_species_main = d.id
			inner JOIN species_sub c ON c.id = a.id_species_sub
			INNER JOIN geolocalisation_country e ON e.id = b.id_geolocalisation_country
			INNER join geolocalisation_city f ON f.id = b.id_geolocalisation_city
			WHERE d.scientific_name = '" . $scientific_name . "'
			ORDER BY c.scientific_name ,  SUM_COUNTS desc";


		$data['sql'] = $sql;

		$res = $_SQL->sql_query($sql);


		$species = "xfgh";
		$count = 0;
		$count1 = 0;
		$count2 = 0;
		$count3 = 0;



		while ( $tab = $_SQL->sql_fetch_array($res, MYSQL_ASSOC) )
		{

			if ( $species != $tab['scientific_name'] )
			{
				$data['species'][$species]['total'] = $count;
				$data['species'][$species]['male'] = $count1;
				$data['species'][$species]['female'] = $count2;
				$data['species'][$species]['unknow'] = $count3;

				$count = 0;
				$count1 = 0;
				$count2 = 0;
				$count3 = 0;
				$species = $tab['scientific_name'];
			}

			$count += $tab['SUM_COUNTS'];
			$count1 += $tab['male'];
			$count2 += $tab['female'];
			$count3 += $tab['unknow'];

			$data['breeder'][] = $tab;
		}

		$data['species'][$species]['total'] = $count;
		$data['species'][$species]['male'] = $count1;
		$data['species'][$species]['female'] = $count2;
		$data['species'][$species]['unknow'] = $count3;


		$this->set('data', $data);
	}

	function admin_captive()
	{
		if ( from() === "administration.controller.php" )
		{
			$module = array();
			$module['picture'] = "administration/2235517.png";
			$module['name'] = __("Wild animals in captivity");
			$module['description'] = __("Manage my birds");

			return $module;
		}

		$_SQL = singleton::getInstance(SQL_DRIVER);

		if ( $_SERVER['REQUEST_METHOD'] == "POST" )
		{

			if ( !empty($_POST['link__species_sub__user_main']) )
			{

				$sql = "DELETE FROM link__species_sub__user_main WHERE id_user_main = " . $GLOBALS['_SITE']['IdUser'] . "";
				$_SQL->sql_query($sql);

				foreach ( $_POST['link__species_sub__user_main'] as $key => $tab )
				{
					$species = array();
					$species['link__species_sub__user_main'] = $tab;

					(empty($species['link__species_sub__user_main']['male'])) ? $species['link__species_sub__user_main']['male'] = 0 : '';
					(empty($species['link__species_sub__user_main']['female'])) ? $species['link__species_sub__user_main']['female'] = 0 : '';
					(empty($species['link__species_sub__user_main']['unknow'])) ? $species['link__species_sub__user_main']['unknow'] = 0 : '';


					$quantity = $species['link__species_sub__user_main']['male']
						+ $species['link__species_sub__user_main']['female']
						+ $species['link__species_sub__user_main']['unknow'];

					if ( $quantity > 0 )
					{
						$species['link__species_sub__user_main']['date_created'] = date('c');
						$species['link__species_sub__user_main']['date_updated'] = date('c');
						$species['link__species_sub__user_main']['id_user_main'] = $GLOBALS['_SITE']['IdUser'];

						if ( $_SQL->sql_save($species) )
						{

							$forsale = array();
							$forsale['link__species_sub__user_main__for_sale'] = $tab;
							$forsale['link__species_sub__user_main__for_sale']['id_user_main'] = $GLOBALS['_SITE']['IdUser'];
							$forsale['link__species_sub__user_main__for_sale']['date_updated'] = date('c');
							$forsale['link__species_sub__user_main__for_sale']['date_created'] = date('c');

							$forsale['link__species_sub__user_main__for_sale']['male'] = $_POST['link__species_sub__user_main__for_sale'][$key]['forsale_male'];
							$forsale['link__species_sub__user_main__for_sale']['female'] = $_POST['link__species_sub__user_main__for_sale'][$key]['forsale_female'];
							$forsale['link__species_sub__user_main__for_sale']['unknow'] = $_POST['link__species_sub__user_main__for_sale'][$key]['forsale_unknow'];
							$forsale['link__species_sub__user_main__for_sale']['price'] = $_POST['link__species_sub__user_main__for_sale'][$key]['price'];

							$quantity2 = $forsale['link__species_sub__user_main__for_sale']['male']
								+ $forsale['link__species_sub__user_main__for_sale']['female']
								+ $forsale['link__species_sub__user_main__for_sale']['unknow'];

							if ( $quantity2 > 0 )
							{
								$is_ok = $_SQL->sql_save($forsale);
								if ( !$is_ok )
								{
									debug($forsale);
									debug($_SQL->sql_error());
									die();
								}
							}


							//exchange
							$exchange = array();
							$exchange['link__species_sub__user_main__exchange'] = $tab;
							$exchange['link__species_sub__user_main__exchange']['id_user_main'] = $GLOBALS['_SITE']['IdUser'];
							$exchange['link__species_sub__user_main__exchange']['date_updated'] = date('c');
							$exchange['link__species_sub__user_main__exchange']['date_created'] = date('c');

							$exchange['link__species_sub__user_main__exchange']['male'] = $_POST['link__species_sub__user_main__exchange'][$key]['exchange_male'];
							$exchange['link__species_sub__user_main__exchange']['female'] = $_POST['link__species_sub__user_main__exchange'][$key]['exchange_female'];
							$exchange['link__species_sub__user_main__exchange']['unknow'] = $_POST['link__species_sub__user_main__exchange'][$key]['exchange_unknow'];

							$quantity3 = $exchange['link__species_sub__user_main__exchange']['male']
								+ $exchange['link__species_sub__user_main__exchange']['female']
								+ $exchange['link__species_sub__user_main__exchange']['unknow'];

							if ( $quantity3 > 0 )
							{
								$_SQL->sql_save($exchange);
							}
						}
						else
						{
							debug($species);
							debug($_SQL->sql_error());
							die();
						}
					}
				}
			}
		}

		$this->title = __("Wild animals in captivity");
		$this->ariane = '> <a href="' . LINK . 'administration/">Administration</a> > ' . $this->title;


		$sql = "SELECT a.*,
			b.male as forsale_male,
			b.female as forsale_female,
			b.unknow as forsale_unknow,
			b.price as forsale_price,
			c.male as exchange_male,
			c.female as exchange_female,
			c.unknow as exchange_unknow,
			d.scientific_name
			
			FROM link__species_sub__user_main a
			INNER JOIN species_main d ON a.id_species_main = d.id
			INNER JOIN species_sub e ON e.id = a.id_species_sub
			LEFT JOIN link__species_sub__user_main__for_sale b ON a.id_user_main = b.id_user_main AND a.id_species_main=b.id_species_main AND	a.id_species_sub=b.id_species_sub
			LEFT JOIN link__species_sub__user_main__exchange c ON a.id_user_main = c.id_user_main AND a.id_species_main=c.id_species_main AND	a.id_species_sub=c.id_species_sub
			WHERE a.id_user_main = " . $GLOBALS['_SITE']['IdUser'] . "
			ORDER BY e.scientific_name";

		$res = $_SQL->sql_query($sql);

		$data = array();
		while ( $tab = $_SQL->sql_fetch_array($res, MYSQL_ASSOC) )
		{
			$sql2 = "SELECT * FROM species_sub where id_species_main = " . $tab['id_species_main'] . " order by scientific_name";
			$res2 = $_SQL->sql_query($sql2);


			$tab_select = array();
			while ( $ob = $_SQL->sql_fetch_object($res2) )
			{
				$select = array();
				$select['id'] = $ob->id;

				$split_sub = explode(' ', $ob->scientific_name);
				$select['libelle'] = $split_sub[2];

				$tab_select[] = $select;
			}

			$tab['list_subspecies'] = $tab_select;
			$data['stock'][] = $tab;
		}


		$data['nbrow'] = $_SQL->sql_num_rows($res);

		if ( $data['nbrow'] == 0 )
		{
			$data['nbrow'] = 1; // to display one line if none in database
		}


		$this->set('data', $data);

		//$this->javascript = array("http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js", "species/admin_captive/species_captive.js", "input_contrain.js");
		$this->javascript = array("jquery-1.6.4.min.js", "species/admin_captive/species_captive.js", "jquery.autocomplete.min.js", "input_constraint.js");

		$this->code_javascript[] = '$("input.species").autocomplete("' . LINK . 'species/get_scientific_name/", {

			mustMatch: true,
			autoFill: true,
			max: 100,
			minChars: 2,
			scrollHeight: 302,
			selectFirst: true,
			delay:300
			}).result(function(event, data, formatted) {
				if (data)
				{
						$(this).next().val(data[1]);
						$(this).parent().next().find("select").load("' . LINK . 'photo/get_options/species_sub/"+data[1]);
				}
			}
		);

$(function() {
	var nbline;
	var derline;
	nbline = $("tr.blah").length;
	derline = nbline;

	$("#add").click( function() {  
		derline++;
		nbline++;
		var clone;
		clone = $("tr.blah:first").clone();
		clone.attr("id","tr-"+derline);
		
		if (derline % 2 == 0)
		{
			clone.addClass("alternate");
		}
		
		clone.find("input.ac_input").attr("id","link__species_sub__user_main-"+derline+"-id_species_main-auto").attr("name","link__species_sub__user_main["+derline+"][id_species_main-auto]").val("");
		clone.find("input.hidden").attr("id","link__species_sub__user_main-"+derline+"-id_species_main").attr("name","link__species_sub__user_main["+derline+"][id_species_main]").val("");
		
		clone.find("select.subspecies").attr("id","link__species_sub__user_main-"+derline+"-id_species_sub").attr("name","link__species_sub__user_main["+derline+"][id_species_sub]").load("' . LINK . 'photo/get_options/species_sub/0"); // pour recuperer le msg dans la langue de l"user
		
		//quantity
		clone.find("input.male").attr("id","link__species_sub__user_main-"+derline+"-male").attr("name","link__species_sub__user_main["+derline+"][male]").val("");
		clone.find("input.female").attr("id","link__species_sub__user_main-"+derline+"-female").attr("name","link__species_sub__user_main["+derline+"][female]").val("");
		clone.find("input.unknow").attr("id","link__species_sub__user_main-"+derline+"-unknow").attr("name","link__species_sub__user_main["+derline+"][unknow]").val("");
		
		//forsale
		clone.find("select.forsale_male").attr("id","link__species_sub__user_main__for_sale-"+derline+"-forsale_male").attr("name","link__species_sub__user_main__for_sale["+derline+"][forsale_male]").val("0");
		clone.find("select.forsale_female").attr("id","link__species_sub__user_main__for_sale-"+derline+"-forsale_female").attr("name","link__species_sub__user_main__for_sale["+derline+"][forsale_female]").val("0");
		clone.find("select.forsale_unknow").attr("id","link__species_sub__user_main__for_sale-"+derline+"-forsale_unknow").attr("name","link__species_sub__user_main__for_sale["+derline+"][forsale_unknow]").val("0");

		clone.find("input.price").attr("id","link__species_sub__user_main__for_sale-"+derline+"-price").attr("name","link__species_sub__user_main__for_sale["+derline+"][price]").val("");
		
		//exchange
		clone.find("select.exchange_male").attr("id","link__species_sub__user_main__exchange-"+derline+"-exchange_male").attr("name","link__species_sub__user_main__exchange["+derline+"][exchange_male]").val("0");
		clone.find("select.exchange_female").attr("id","link__species_sub__user_main__exchange-"+derline+"-exchange_female").attr("name","link__species_sub__user_main__exchange["+derline+"][exchange_female]").val("0");
		clone.find("select.exchange_unknow").attr("id","link__species_sub__user_main__exchange-"+derline+"-exchange_unknow").attr("name","link__species_sub__user_main__exchange["+derline+"][exchange_unknow]").val("0");

		clone.find("input.delete-line").attr("id","delete-"+derline);
		
		$("#variante").append(clone); 
		$("#nb_line").attr("value",derline);
		$("input.delete-line").attr("disabled",false).removeClass("btGrey").addClass("btBlueTest");
		
		$("input.species").autocomplete("' . LINK . 'species/get_scientific_name/", {
			mustMatch: true,
			autoFill: true,
			max: 100,
			minLength: 2,
			scrollHeight: 302,
			selectFirst: true,
			delay:400
			}).result(function(event, data, formatted) {
				if (data)
				{
					$(this).next().val(data[1]);
					$(this).parent().next().find("select").load("' . LINK . 'photo/get_options/species_sub/"+data[1]);
				}
			}
		);
	});


	$("input.delete-line").live("click", function() {
		if (nbline > 1) {
			var numLigne = $(this).attr("id").match(/delete-(\d+)/)[1];
			$("#tr-" + numLigne).remove(); 
			nbline--;
			$("#add").attr("disabled",false);
			if (nbline === 1){
				$("input.delete-line").attr("disabled",true).removeClass("btBlueTest").addClass("btGrey");
			}
		}
	});
}); 


';
	}

	function get_scientific_name()
	{
		/*
		  [path] => en/user/city/
		  [q] => paris
		  [limit] => 10
		  [timestamp] => 1297207840432
		  [lg] => en
		  [url] => user/city/
		 */


		$this->layout_name = false;
		$this->view = false;
		$_SQL = Singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT scientific_name, id 
			FROM species_main 
		WHERE scientific_name LIKE '" . $_SQL->sql_real_escape_string($_GET['q']) . "%' AND id_history_etat = 1
		ORDER BY scientific_name LIMIT 0,100";
		$res = $_SQL->sql_query($sql);

		while ( $ob = $_SQL->sql_fetch_object($res) )
		{
			echo $ob->scientific_name . "|" . $ob->id . "\n";
		}
	}

	function admin_species()
	{

		if ( from() === "administration.controller.php" )
		{
			$module = array();
			$module['picture'] = "administration/199332-bird-saver.png";
			$module['name'] = __("Species");
			$module['description'] = __("Organize the taxons");

			return $module;
		}


		$this->title = __("Species");
		$this->ariane = "> " . $this->title;

		$_SQL = singleton::getInstance(SQL_DRIVER);

		$sql = "SELECT a.scientific_name as genus, b.scientific_name as species, c.scientific_name as subspecies, a.id as id_genus, b.id as id_species, c.id as id_subspecies,
			a.is_valid as valid_genus, b.is_valid as valid_species, c.is_valid as valid_subspecies
			FROM species_genus a
			INNER JOIN species_main b ON a.id = b.id_species_genus
			INNER JOIN species_sub c ON b.id = c.id_species_main
			WHERE a.id_species_family = 438
			ORDER BY a.scientific_name, b.scientific_name, c.scientific_name";
		$res = $_SQL->sql_query($sql);

		$this->data['species'] = $_SQL->sql_to_array($res);

		$this->set("data", $this->data);
	}

	function waiting($param)
	{

		$_SQL = singleton::getInstance(SQL_DRIVER);


		$sql = "SELECT z.*,
		count(distinct b.id_species_picture_id) as cpt
		FROM species_picture_search a
		INNER JOIN species_source_main z ON z.id = a.id_species_source_main
		inner join link__species_picture_id__species_picture_search b ON b.id_species_picture_search = a.id
		INNER JOIN species_main c ON c.id = a.id_species_main
		WHERE c.scientific_name = '" . str_replace('_', ' ', $_SQL->sql_real_escape_string($param[0])) . "'
		GROUP BY a.id_species_source_main";

		$res = $_SQL->sql_query($sql);
		$this->data['pending'] = $_SQL->sql_to_array($res);
		$this->data['param'] = $param;



		$this->set("data", $this->data);
	}

}

