<?php

use \glial\synapse\Singleton;
use \glial\synapse\Controller;

class Graphiz extends Controller
{

    function index()
    {
        $this->view = false;
        $this->layout_name = false;

        $_SQL = Singleton::getInstance(SQL_DRIVER);

        $sql = "SELECT TABLE_NAME, REFERENCED_TABLE_NAME
FROM information_schema.referential_constraints
WHERE constraint_schema =  'species' order by REFERENCED_TABLE_NAME desc, TABLE_NAME";

        $res = $_SQL->sql_query($sql);


        $fp = fopen("test.dot", "w");

        if ( $fp ) {
            fwrite($fp, "graph test {\n");
            fwrite($fp, "sep=\"+150\"\n");
            fwrite($fp, "overlap=scalexy\n");
            fwrite($fp, "splines=true");
            //fwrite($fp, "nodesep=1.6");

            $entity = array();
            while ( $ob = $_SQL->sql_fetch_object($res) ) {
                fwrite($fp, $ob->TABLE_NAME . " -- " . $ob->REFERENCED_TABLE_NAME . "\n");
            }

            fwrite($fp, "}");

            $ret = shell_exec("dot -Tpng test.dot -o image/test.png");
            echo $ret;

            echo '<img src="' . IMG . '/test.png" />';
        }
    }

}