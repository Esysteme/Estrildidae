<?php

use \glial\synapse\singleton;

class administration extends controller
{

	public $module_group = "Administration";

	function index()
	{

		$this->layout_name = "admin";
		$this->title = __("Administration");
		$this->ariane = "> " . $this->title;
		$dir = APP_DIR . DS . "controller/";
		// Add your class dir to include path
		set_include_path($dir);
		// You can use this trick to make autoloader look for commonly used "My.class.php" type filenames
		spl_autoload_extensions('.controller.php');
		// Use default autoload implementation 		 		
		spl_autoload_register();

		if ( is_dir($dir) )
		{
			$dh = opendir($dir);

			if ( $dh )
			{

				include_once LIBRARY . 'Glial/acl/acl.php';
				$acl = new acl($GLOBALS['_SITE']['id_group']);

				while ( ($file = readdir($dh)) !== false )
				{
					if ( strstr($file, '.controller.php') )
					{

						if ( filetype($dir . $file) != "file" )
						{
							continue;
						}

						//spl_autoload($dir . $file);
						$class_name = explode(".", $file);
						$nom = $class_name[0];
						$class = new $nom("", "", "");
						$tab = get_class_methods($nom);
						$tab2 = get_class_methods("Controller");
						$tab3 = array_diff($tab, $tab2);
						foreach ( $tab3 as $name )
						{

							if ( $acl->is_allowed($nom, $name) )
							{

								if ( strstr($name, 'admin') )
								{

									if ( property_exists($nom, "module_group") )
									{
										$tmp = $class->$name();
										$this->data['link'][$class->module_group][$tmp['name']] = $class->$name();
										$this->data['link'][$class->module_group][$tmp['name']]['url'] = $nom . "/" . $name . "/";
									}
								}
							}
						}

						//echo "fichier : $file : type : " . filetype($dir . $file) . "\n<br />";
					}
				}

				closedir($dh);
			}
		}

		$this->set("data", $this->data);
	}

	function admin_table()
	{

		if (ISCLI)
		{

			$this->view = false;
			$this->layout_name = false;
		}


		$module = array();
		$module['picture'] = "administration/tables.png";
		$module['name'] = __("Tables");
		$module['description'] = __("Make the dictionary of field");

		if ( from() !== "administration.controller.php" )
		{

			if ( true ) //ENVIRONEMENT
			{
				$dir = TMP . "database/";

				if ( is_dir($dir) )
				{
					$dh = opendir($dir);
					if ( $dh )
					{
						while ( ($file = readdir($dh)) !== false )
						{

							if ( substr($file, 0, 1) === "." )
							{
								continue;
							}

							unlink($dir . $file);
						}
					}
				}

				$sql = "SHOW TABLES";
				$res = $GLOBALS['_SQL']->sql_query($sql);
				while ( $table = $GLOBALS['_SQL']->sql_fetch_array($res) )
				{
					$fp = fopen(TMP . "/database/" . $table[0] . ".table.txt", "w");
					$sql = "DESCRIBE `" . $table[0] . "`";
					$res2 = $GLOBALS['_SQL']->sql_query($sql);
					while ( $ob = $GLOBALS['_SQL']->sql_fetch_object($res2) )
					{
						$data['field'][] = $ob->Field;
					}

					$data = serialize($data);
					fwrite($fp, $data);
					fclose($fp);
					unset($data);
				}
			}
		}

		return $module;
	}

	function admin_init()
	{
		$module = array();
		$module['picture'] = "administration/gear_32.png";
		$module['name'] = __("Access Control List");
		$module['description'] = __("Update the right of users and groups");


		if (ISCLI)
		{

				$this->view = false;
				$this->layout_name = false;
		}



		if ( from() !== "administration.controller.php" )
			$this->init();
		//echo from();
		return $module;
	}

	function init()
	{

		$_SQL = singleton::getInstance(SQL_DRIVER);
		
		
		if ( true ) //ENVIRONEMENT
		{
			echo "--".APP_DIR;
			
			$dir = APP_DIR . DS . "controller" . DS;
			$sql = "TRUNCATE TABLE acl_controller";
			$GLOBALS['_SQL']->sql_query($sql);
			$sql = "TRUNCATE TABLE acl_action";
			$GLOBALS['_SQL']->sql_query($sql);
			$sql = "TRUNCATE TABLE acl_action_group";
			$GLOBALS['_SQL']->sql_query($sql);

			if ( is_dir($dir) )
			{
				$dh = opendir($dir);
				if ( $dh )
				{
					while ( ($file = readdir($dh)) !== false )
					{
						if ( strstr($file, '.controller.php') )
						{

							if ( filetype($dir . $file) != "file" || substr($file, 0, 1) === "." )
							{
								continue;
							}

							$class_name = explode(".", $file);
							$name = $class_name[0];

							if ( !class_exists($name) )
							{
								include($dir . $file);
							}

							$tab = get_class_methods($name);
							$tab2 = get_class_methods("Controller");
							$tab3 = array_diff($tab, $tab2);

							$acl_controller = array();
							$acl_controller['acl_controller']['name'] = $name;

							
							$acl_action['acl_action']['id_acl_controller'] = $_SQL->sql_save($acl_controller);
							
							if ( ! $acl_action['acl_action']['id_acl_controller'])
							{
								echo $file . " : already exist " . $acl_action['acl_action']['id_acl_controller'] . "<br />";
							}

							unset($acl_controller['acl_controller']);
							foreach ( $tab3 as $name )
							{
								$acl_action['acl_action']['name'] = $name;

								if ( ! $_SQL->sql_save($acl_action) )
								{
									echo "&nbsp;&nbsp;&nbsp;&nbsp;" . $name . " : already exist<br />";
								}
							}

							/*							 * ****** */  //echo "fichier : $file : type : " . filetype($dir . $file) . "\n<br />";
						}
					}

					closedir($dh);
				}
			}

			/*
			  Visitor
			  Member
			  Administrator
			  Super administrator
			 */
			$sql = "TRUNCATE TABLE acl_action_group";
			$GLOBALS['_SQL']->sql_query($sql);
			// &#2157;etre dans un fichier de config ?
			$this->add_acl("Super administrator", "*");
			$this->add_acl("Member", "user/block_last_registered");
			$this->add_acl("Member", "user/block_last_online");
			$this->add_acl("Member", "forum/");
			$this->add_acl("Member", "user/index");
			$this->add_acl("Member", "home/");
			$this->add_acl("Member", "species/");
			$this->add_acl("Member", "home/");
			$this->add_acl("Member", "who_we_are/");
			$this->add_acl("Member", "media/");
			$this->add_acl("Member", "download/");
			$this->add_acl("Member", "search/");
			$this->add_acl("Member", "partner/");
			$this->add_acl("Member", "contact_us/");
			$this->add_acl("Member", "faq/");
			$this->add_acl("Member", "user/is_logged");
			$this->add_acl("Member", "user/login");
			$this->add_acl("Member", "user/city");
			$this->add_acl("Member", "user/author");
			$this->add_acl("Member", "user/block_newsletter");
			$this->add_acl("Member", "user/block_last_registered");
			$this->add_acl("Member", "user/profil");
			$this->add_acl("Member", "user/mailbox");
			$this->add_acl("Member", "administration/index");
			$this->add_acl("Member", "photo/admin_crop");
			$this->add_acl("Member", "photo/get_options");
			$this->add_acl("Member", "photo/index");
			$this->add_acl("Member", "translation/admin_translation");
			$this->add_acl("Member", "user/user_main");
			$this->add_acl("Member", "author/");
			$this->add_acl("Visitor", "author/");
			$this->add_acl("Visitor", "species/");
			$this->add_acl("Visitor", "home/");
			$this->add_acl("Visitor", "who_we_are/");
			$this->add_acl("Visitor", "media/");
			$this->add_acl("Visitor", "download/");
			$this->add_acl("Visitor", "search/");
			$this->add_acl("Visitor", "partner/");
			$this->add_acl("Visitor", "contact_us/");
			$this->add_acl("Visitor", "faq/");
			$this->add_acl("Visitor", "user/register");
			$this->add_acl("Visitor", "user/lost_password");
			$this->add_acl("Visitor", "user/is_logged");
			$this->add_acl("Visitor", "user/login");
			$this->add_acl("Visitor", "user/city");
			$this->add_acl("Visitor", "user/block_newsletter");
			$this->add_acl("Visitor", "user/confirmation");
			$this->add_acl("Visitor", "user/password_recover");
			$sql = "SELECT id_group, b.name as id_action, c.name as id_controller FROM acl_action_group a
		INNER JOIN acl_action b ON a.id_acl_action = b.id
		INNER JOIN acl_controller c ON c.id = b.id_acl_controller";
			$res = $GLOBALS['_SQL']->sql_query($sql);

			$data = array();

			while ( $ob = $GLOBALS['_SQL']->sql_fetch_object($res) )
			{
				$data[$ob->id_group][$ob->id_controller][$ob->id_action] = 1;
			}

			$dir = TMP . "acl" . DS . "acl.txt";
			file_put_contents($dir, serialize($data));
		}
		else
		{
			set_flash("error", __("Error"), __("Init unavailable in mode production, turn ENVIRONEMENT to true in configuration/environement.php"));
			header("location: " . LINK . "home/index/");
			die();
		}

		//return $module;
	}

	private function add_acl($group, $tree)
	{
		$tree_id = explode("/", $tree);
		/*
		  debug($tree);
		  debug(count($tree));
		  debug($tree_id);
		  debug(count($tree_id));
		 */   //test if not exist

		if ( count($tree_id) == 1 )
		{
			$sql = "select count(1) as cpt from `group` where name = '" . $group . "'";
			$res = $GLOBALS['_SQL']->sql_query($sql);
			$ob = $GLOBALS['_SQL']->sql_fetch_object($res);

			if ( $ob->cpt != 1 )
			{
				die("Group unknow !");
			}
		}
		elseif ( count($tree_id) == 2 )
		{

			if ( $tree_id['1'] === "" )
			{
				$sql = "select count(1) as cpt from `acl_controller` where name = '" . $tree_id['0'] . "'";
				$res = $GLOBALS['_SQL']->sql_query($sql);
				$ob = $GLOBALS['_SQL']->sql_fetch_object($res);

				if ( $ob->cpt < 1 )
				{
					die("Controller unknow !");
				}
			}
			else
			{
				$sql = "select count(1) as cpt from `acl_action` where name = '" . $tree_id['1'] . "'";
				$res = $GLOBALS['_SQL']->sql_query($sql);
				$ob = $GLOBALS['_SQL']->sql_fetch_object($res);

				if ( $ob->cpt < 1 )
				{
					echo "group : " . $group . "<br />";
					die("Acion unknow (" . $tree_id['1'] . ") !");
				}
			}
		}


		if ( count($tree_id) == 1 )
		{
			$sql = "REPLACE INTO acl_action_group (id_acl_action, id_group) SELECT b.id as acl_action, c.id FROM acl_controller a
		INNER JOIN acl_action b ON a.id = b.id_acl_controller
		LEFT JOIN `group` c on 1 = 1
		WHERE c.name = '" . $group . "'";
		}
		else
		if ( count($tree_id) == 2 )
		{

			if ( $tree_id['1'] === "" )
			{
				$sql = "REPLACE INTO acl_action_group (id_acl_action, id_group) SELECT b.id as acl_action, c.id FROM acl_controller a
			INNER JOIN acl_action b ON a.id = b.id_acl_controller
			LEFT JOIN `group` c on 1 = 1
			WHERE c.name = '" . $group . "' AND a.name = '" . $tree_id['0'] . "'";
			}
			else
			{
				$sql = "REPLACE INTO acl_action_group (id_acl_action, id_group) SELECT b.id as acl_action, c.id FROM acl_controller a
			INNER JOIN acl_action b ON a.id = b.id_acl_controller
			LEFT JOIN `group` c on 1 = 1
			WHERE c.name = '" . $group . "' AND a.name = '" . $tree_id['0'] . "' AND b.name = '" . $tree_id['1'] . "'";
			}
		}
		else
		{
			die("Must be XX/YY with last '/'  XX/YY/");
		}

		$GLOBALS['_SQL']->sql_query($sql);
	}

	function generate_model()
	{

		//php index.php administration generate_model

		$this->layout_name = false;

		$sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA ='species' and TABLE_TYPE = 'BASE TABLE'";
		$res = $GLOBALS['_SQL']->sql_query($sql);

		while ( $ob2 = $GLOBALS['_SQL']->sql_fetch_object($res) )
		{

			$table = $ob2->TABLE_NAME;

			$file = APP_DIR . "/model/" . $table . ".php";

			if ( !file_exists($file) )
			{
				$fp = fopen($file, "w");

				echo "FILE : " . $file . "\n";

				$text = "<?php\n\nnamespace application\model;
use glial\synapse\model;

class " . $table . " extends model\n{\nvar \$schema = \"";

				$sql = "SHOW CREATE TABLE `" . $table . "`";
				$res2 = $GLOBALS['_SQL']->sql_query($sql);

				$array = $GLOBALS['_SQL']->sql_fetch_array($res2);

				$sql = "DESCRIBE `" . $table . "`";
				$res3 = $GLOBALS['_SQL']->sql_query($sql);

				$i = 0;

				unset($data);
				unset($field);

				while ( $ob = $GLOBALS['_SQL']->sql_fetch_object($res3) )
				{
					$field[] = "\"" . $ob->Field . "\"";

					$data[$table][$i]['field'] = $ob->Field;
					$data[$table][$i]['type'] = $ob->Type;
					$i++;
				}

				$text .= $array[1];
				$text .= "\";\n\nvar \$field = array(" . implode(",", $field) . ");\n\nvar \$validate = array(\n";

				foreach ( $data[$table] as $field )
				{
					if ( $field['field'] == "id" )
					{
						continue;
					}
					if ( mb_substr($field['field'], 0, 2) === "id" )
					{
						$text .= "\t'" . $field['field'] . "' => array(\n\t\t'reference_to' => array('The constraint to " . mb_substr($field['field'], 3) . ".id isn\'t respected.','" . mb_substr($field['field'], 3) . "', 'id')\n\t),\n";
					}
					elseif ( mb_substr($field['field'], 0, 2) === "ip" )
					{
						$text .= "\t'" . $field['field'] . "' => array(\n\t\t'ip' => array('your IP is not valid')\n\t),\n";
					}
					elseif ( $field['field'] === "email" )
					{
						$text .= "\t'" . $field['field'] . "' => array(\n\t\t'email' => array('your email is not valid')\n\t),\n";
					}
					else
					{

						if ( mb_strstr($field['type'], "int") )
						{
							$text .= "\t'" . $field['field'] . "' => array(\n\t\t'numeric' => array('This must be an int.')\n\t),\n";
						}
						elseif ( mb_strstr($field['type'], "time") )
						{
							$text .= "\t'" . $field['field'] . "' => array(\n\t\t'time' => array('This must be a time.')\n\t),\n";
						}
						elseif ( mb_strstr($field['type'], "date") )
						{
							$text .= "\t'" . $field['field'] . "' => array(\n\t\t'date' => array('This must be a date.')\n\t),\n";
						}
						elseif ( mb_strstr($field['type'], "datetime") )
						{
							$text .= "\t'" . $field['field'] . "' => array(\n\t\t'not_empty' => array('This must be a date time.')\n\t),\n";
						}
						elseif ( mb_strstr($field['type'], "float") )
						{
							$text .= "\t'" . $field['field'] . "' => array(\n\t\t'decimal' => array('This must be a float.')\n\t),\n";
						}
						else
						{
							$text .= "\t'" . $field['field'] . "' => array(\n\t\t'not_empty' => array('This field is requiered.')\n\t),\n";
						}
					}
				}

				$text .= ");\n\nfunction get_validate()\n{\nreturn \$this->validate;\n}\n}\n";

				fwrite($fp, $text);
				fclose($fp);

				unset($data);
			}
		}
	}
	
	function save_database()
	{
		$this->layout_name = false;
		$this->view = false;

		$path = "/home/www/arkadin/data/database/arkadin/";


		$sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA ='arkadin' and TABLE_TYPE = 'BASE TABLE'";
		$res = $GLOBALS['_SQL']->sql_query($sql);
		while ( $ob = $GLOBALS['_SQL']->sql_fetch_object($res) )
		{
			shell_exec("mkdir -p " . $path . "structure/table/" . $ob->TABLE_NAME);


			// create structure table
			$sql2 = "SHOW CREATE TABLE `" . $ob->TABLE_NAME . "`;";
			$res2 = $GLOBALS['_SQL']->sql_query($sql2);

			while ( $ob2 = $GLOBALS['_SQL']->sql_fetch_array($res2, MYSQL_NUM) )
			{
				echo 'create table : ' . $ob->TABLE_NAME . "\n";

				file_put_contents($path . "structure/table/" . $ob->TABLE_NAME . "/table.sql", $ob2[1].";");
			}

			// create index table

			$sql3 = "SHOW INDEXES FROM `" . $ob->TABLE_NAME . "`";
			$res3 = $GLOBALS['_SQL']->sql_query($sql3);


			while ( $ob3 = $GLOBALS['_SQL']->sql_fetch_object($res3) )
			{
				echo 'create indexes : ' . $ob->TABLE_NAME . "\n";


				if ( $ob3->Key_name == "PRIMARY" )
				{
					$index[] = "ALTER TABLE `" . $ob->TABLE_NAME . "` ADD PRIMARY KEY (  `" . $ob3->Column_name . "` );";
				}
				else
				{
					if ( $ob3->Non_unique == "1" )
					{
						$index[] = "CREATE UNIQUE INDEX `".$ob3->Key_name."`  ON `" . $ob->TABLE_NAME . "` (  `" . $ob3->Column_name . "` );";
					}
					else
					{
						$index[] = "CREATE INDEX `".$ob3->Key_name."` ON `" . $ob->TABLE_NAME . "` (  `" . $ob3->Column_name . "` );";
					}
				}
			}

			file_put_contents($path . "structure/table/" . $ob->TABLE_NAME . "/index.sql", implode("\n",$index));
		}

		$sql33 = "SELECT * FROM INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA ='arkadin' and TABLE_TYPE = 'VIEW'";
		$res33 = $GLOBALS['_SQL']->sql_query($sql33);
		while ( $ob33 = $GLOBALS['_SQL']->sql_fetch_object($res33) )
		{
			shell_exec("mkdir -p " . $path . "structure/view/" . $ob33->TABLE_NAME);
		}
	}

	function insert_backup_table()
	{
		$this->view = false;
		$this->layout_name = false;

		include_once(LIBRARY . "Glial/sgbd/mysql/backup.php");
		include_once (LIB . "wlHtmlDom.php");

		$_SQL = singleton::getInstance(SQL_DRIVER);

		$data = glial\sgbd\mysql\backup::insert();

		//debug($data);
	}



}
