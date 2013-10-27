<?php

use \Glial\Synapse\Singleton;
use \Glial\Acl\Acl;
use \Glial\Synapse\Controller;

class Administration extends Controller
{

    public $module_group = "Administration";

    function index()
    {

        $this->layout_name = "admin";
        $this->title = __("Administration");
        $this->ariane = "> " . $this->title;
        $dir = APP_DIR . DS . "controller";
        // Add your class dir to include path


        if (is_dir($dir)) {

            $acl = new Acl($GLOBALS['_SITE']['id_group']);

            $path = $dir . "/*.controller.php";
            $list_class = glob($path);

            //$method_class_controller = get_class_methods("\Glial\Synapse\Controller");

            foreach ($list_class as $file) {
                if (strstr($file, '.controller.php')) {

                    $full_name = pathinfo($file);
                    list($className, ) = explode(".", $full_name['filename']);


                    if ($className != __CLASS__) {
                        require($file);
                    }

                    $class = new ReflectionClass($className);
                    $tab_methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);


                    $methods = array();
                    foreach ($tab_methods as $method) {
                        if ($method->class === $className) {

                            if (strstr($method->name, 'admin')) {
                                $methods[] = $method->name;
                            }
                        }
                    }

                    //$tab3 = array_diff($methods, $method_class_controller);

                    foreach ($methods as $name) {

                        if ($acl->isAllowed($className, $name)) {



                            if (property_exists($className, "module_group")) {

                                $admin = new $className("", "", "");

                                $tmp = $admin->$name();
                                $this->data['link'][$admin->module_group][$tmp['name']] = $admin->$name();
                                $this->data['link'][$admin->module_group][$tmp['name']]['url'] = $className . "/" . $name . "/";
                            }
                        }
                    }

                    // echo "memory : " . (memory_get_usage() / 1024 / 1024) . " M  fichier : $file : type : " . filetype($file) . "\n<br />";
                }
            }
        }


        $this->set("data", $this->data);
    }


    function save_database()
    {
        $this->layout_name = false;
        $this->view = false;

        $path = "/home/www/arkadin/data/database/arkadin/";


        $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA ='arkadin' and TABLE_TYPE = 'BASE TABLE'";
        $res = $this->db['mysql_write']->sql_query($sql);
        while ($ob = $this->db['mysql_write']->sql_fetch_object($res)) {
            shell_exec("mkdir -p " . $path . "structure/table/" . $ob->TABLE_NAME);


            // create structure table
            $sql2 = "SHOW CREATE TABLE `" . $ob->TABLE_NAME . "`;";
            $res2 = $this->db['mysql_write']->sql_query($sql2);

            while ($ob2 = $this->db['mysql_write']->sql_fetch_array($res2, MYSQL_NUM)) {
                echo 'create table : ' . $ob->TABLE_NAME . "\n";

                file_put_contents($path . "structure/table/" . $ob->TABLE_NAME . "/table.sql", $ob2[1] . ";");
            }

            // create index table

            $sql3 = "SHOW INDEXES FROM `" . $ob->TABLE_NAME . "`";
            $res3 = $this->db['mysql_write']->sql_query($sql3);


            while ($ob3 = $this->db['mysql_write']->sql_fetch_object($res3)) {
                echo 'create indexes : ' . $ob->TABLE_NAME . "\n";


                if ($ob3->Key_name == "PRIMARY") {
                    $index[] = "ALTER TABLE `" . $ob->TABLE_NAME . "` ADD PRIMARY KEY (  `" . $ob3->Column_name . "` );";
                } else {
                    if ($ob3->Non_unique == "1") {
                        $index[] = "CREATE UNIQUE INDEX `" . $ob3->Key_name . "`  ON `" . $ob->TABLE_NAME . "` (  `" . $ob3->Column_name . "` );";
                    } else {
                        $index[] = "CREATE INDEX `" . $ob3->Key_name . "` ON `" . $ob->TABLE_NAME . "` (  `" . $ob3->Column_name . "` );";
                    }
                }
            }

            file_put_contents($path . "structure/table/" . $ob->TABLE_NAME . "/index.sql", implode("\n", $index));
        }

        $sql33 = "SELECT * FROM INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA ='arkadin' and TABLE_TYPE = 'VIEW'";
        $res33 = $this->db['mysql_write']->sql_query($sql33);
        while ($ob33 = $this->db['mysql_write']->sql_fetch_object($res33)) {
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

    private function rem_acl($group, $tree)
    {
        $tree_id = explode("/", $tree);


        if (count($tree_id) == 1) {
            $sql = "select count(1) as cpt from `group` where name = '" . $group . "'";
            $res = $this->db['mysql_write']->sql_query($sql);
            $ob = $this->db['mysql_write']->sql_fetch_object($res);

            if ($ob->cpt != 1) {
                die("Group unknow !");
            }
        } elseif (count($tree_id) == 2) {
            $tree_id['0'] = \Glial\Utility\Inflector::camelize($tree_id['0']);

            if ($tree_id['1'] === "") {
                $sql = "select count(1) as cpt from `acl_controller` where name = '" . $tree_id['0'] . "'";
                $res = $this->db['mysql_write']->sql_query($sql);
                $ob = $this->db['mysql_write']->sql_fetch_object($res);

                if ($ob->cpt < 1) {
                    die("Controller unknow !");
                }
            } else {
                $sql = "select count(1) as cpt from `acl_action` where name = '" . $tree_id['1'] . "'";
                $res = $this->db['mysql_write']->sql_query($sql);
                $ob = $this->db['mysql_write']->sql_fetch_object($res);

                if ($ob->cpt < 1) {
                    echo "group : " . $group . "<br />";
                    die("Acion unknow (" . $tree_id['1'] . ") !");
                }
            }
        }


        if (count($tree_id) == 1) {
            $sql = "DELETE FROM acl_action_group a
		INNER JOIN `group` c on c.id = a.id
		WHERE c.name = '" . $group . "'";
        } else
        if (count($tree_id) == 2) {

            if ($tree_id['1'] === "") {
                $sql = "DELETE a.* FROM acl_action_group a
                INNER JOIN `group` c on c.id = a.id
                INNER JOIN acl_action
                WHERE c.name = '" . $group . "' AND a.name = '" . $tree_id['0'] . "'";
            } else {
                $sql = "DELETE a.* 
                    FROM acl_action_group a
                LEFT JOIN `group` c on 1 = 1
               inner join acl_action e ON e.id = a.id_acl_action
                INNER JOIN acl_controller d ON d.id = e.id_acl_controller
			WHERE c.name = '" . $group . "' AND d.name = '" . $tree_id['0'] . "' AND e.name = '" . $tree_id['1'] . "'";
                
                
                echo $sql."\n";
            }
        } else {
            die("Must be XX/YY with last '/'  XX/YY/");
        }

        $this->db['mysql_write']->sql_query($sql);
    }

}

