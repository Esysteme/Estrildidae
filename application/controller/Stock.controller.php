<?php

use \glial\synapse\Controller;

class Stock extends Controller
{

    public $module_group = "Other";

    function index()
    {
        $sql = "SELECT sum(male), sum(female), sum(unknow),a.id_species_main, a.id_species_sub,b.scientific_name from link__species_sub__user_main a
            inner join species_sub b on a.id_species_sub = b.id
        group by a.id_species_main, a.id_species_sub
        order by b.scientific_name";
        
        
        $res = $GLOBALS['_SQL']->sql_query($sql);
        $data['list_stock'] = $GLOBALS['_SQL']->sql_to_array($res);
        
        $this->set('data',$data);
        
    }
    

}