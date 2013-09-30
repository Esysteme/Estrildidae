<?php

use \Glial\synapse\Controller;

class Convert extends Controller
{

    function index()
    {
        
    }

    function updateModel()
    {
        $this->view = false;
        $this->layout_name = false;

        $files = glob(APP_DIR . DS . "model" . DS . "*.php");

        foreach ($files as $filename) {
            echo $filename . EOL;

            $data = file_get_contents($filename);
            
            $data = str_replace('extends model', 'extends Model', $data);
            $data = str_replace('use glial\synapse\model;', 'use \Glial\Synapse\Model;', $data);
            $data = str_replace('namespace application\model;', 'namespace Application\Model;', $data);

            file_put_contents($filename, $data);
        }
    }
    
    function upgradeController()
    {
        $this->view = false;
        $this->layout_name = false;

        $files = glob(APP_DIR . DS . "controller" . DS . "*.php");

        foreach ($files as $filename) {
            

            if (preg_match("#Convert#i", $filename))
            {
                continue;
            }
            
            echo $filename . EOL;
            
            $data = file_get_contents($filename);
            
            $data = str_replace('$GLOBALS[\'_SQL\']->', '$this->db[\'mysql_write\']->', $data);
            $data = str_replace('$_SQL->', '$this->db[\'mysql_write\']->', $data);
            $data = str_replace('$_SQL = Singleton::getInstance(SQL_DRIVER);', '', $data);
            
            file_put_contents($filename, $data);
        }
    }
}