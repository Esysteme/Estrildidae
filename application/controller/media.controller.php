<?phpif (!defined('SIZE_BACKUP')){	define("SIZE_BACKUP", 1024);}if (!defined('SIZE_SITE_MAX')){	define("SIZE_SITE_MAX", 890);}if (!defined('SIZE_MINIATURE_BIG')){	define("SIZE_MINIATURE_BIG", 250);}if (!defined('SIZE_MINIATURE_SMALL')){	define("SIZE_MINIATURE_SMALL", 158);}class media extends Controller {	public $module_group = "Media";	public $method_administration = array("user", "roles");	function index() {		$this->layout_name = "admin";		$this->title = __("Medias");		$this->ariane = "> " . $this->title;	}	function photo() {		include_once LIB . 'imageprocessor.lib.php';		$_SQL = Singleton::getInstance(SQL_DRIVER);		$this->layout_name = "admin";		$this->title = __("Photos");		$this->ariane = "> " . __("Medias") . " > " . $this->title;		empty($_GET['page']) ? $data['page'] = 1 : $data['page'] = $_GET['page'];		/*		  $sql = "SELECT *, a.id as id_photo from species_picture_main a		  INNER JOIN species_tree_name b ON a.id_species_main = b.id		  INNER JOIN species_translation z ON z.id_row = a.id_species_main and id_table = 7		  WHERE a.id_history_etat = 1		  ORDER BY date_validated DESC LIMIT 0,20";		  $res = $_SQL->sql_query($sql);		 */// id photo 475		$sql1 = "SELECT *, a.id as id_photo ";		$sql2 = "SELECT count(1) as cpt ";		$sql = " from species_picture_main a		INNER JOIN species_tree_name b ON a.id_species_main = b.id		INNER JOIN species_author c ON a.id_author = c.id		INNER JOIN licence d ON d.id = a.id_licence		LEFT JOIN species_translation z ON z.id_row = a.id_species_main and id_table = 7		WHERE a.id_history_etat = 1 ";		$sql3 = " order by a.date_validated desc";		$res = $GLOBALS['_SQL']->sql_query($sql2 . $sql);		$data['count'] = $GLOBALS['_SQL']->sql_to_array($res);		//*****************************pagination		if ($data['count'][0]['cpt'] != 0)		{			include_once(LIB . "pagination.lib.php");			//url, curent page, nb item max , nombre de lignes, nombres de pages			$pagination = new pagination(LINK . __CLASS__ . '/' . __FUNCTION__, $data['page'], $data['count'][0]['cpt'], 20, 10);			$tab = $pagination->get_sql_limit();			$pagination->set_alignment("left");			$pagination->set_invalid_page_number_text(__("Please input a valid page number!"));			$pagination->set_pages_number_text(__("pages of"));			$pagination->set_go_button_text(__("Go"));			$pagination->set_first_page_text("« " . __("First page"));			$pagination->set_last_page_text(__("Last page") . " »");			$pagination->set_next_page_text("»");			$pagination->set_prev_page_text("«");			$data['pagination'] = $pagination->print_pagination();			$limit = " LIMIT " . $tab[0] . "," . $tab[1] . " ";			$data['i'] = $tab[0] + 1;			//*****************************pagination end		}		empty($limit) ? $limit = "" : "";		$res = $GLOBALS['_SQL']->sql_query($sql1 . $sql . $sql3 . $limit);		$data['species_picture_main'] = $GLOBALS['_SQL']->sql_to_array($res);		if ($GLOBALS['_SITE']['IdUser'] != -1)		{						foreach ($data['species_picture_main'] as $value)			{				$sql = "SELECT a.date, c.title, c.point,b.name, b.id, b.firstname,d.iso 			FROM history_main a			INNER JOIN user_main b ON a.id_user_main = b.id			INNER JOIN history_action c ON c.id = a.id_history_action			INNER JOIN geolocalisation_country d ON b.id_geolocalisation_country = d.id			WHERE line = " . $value['id_photo'] . " AND id_history_table in(9,10)			ORDER BY a.id asc";				$res = $_SQL->sql_query($sql);				$data['history'][$value['id_photo']] = $_SQL->sql_to_array($res);				//echo $sql;			}		}		$this->set("data", $data);	}	/*	  SELECT a.date, c.title, c.point,b.name, b.id, b.firstname,d.iso	  FROM history_main a	  INNER JOIN user_main b ON a.id_user_main = b.id	  INNER JOIN history_action c ON c.id = a.id_history_action	  INNER JOIN geolocalisation_country d ON b.id_geolocalisation_country = d.id	  WHERE line = 281 AND id_history_table = 10	  SELECT a.date, c.title, c.point,b.name, b.id, b.firstname,d.iso FROM history_main a INNER JOIN user_main b ON a.id_user_main = b.id INNER JOIN history_action c ON c.id = a.id_history_action INNER JOIN geolocalisation_country d ON b.id_geolocalisation_country = d.id WHERE line = 120590 AND id_history_table = 10	 */	function video() {			}	function sound() {			}	function articles() {			}}?>