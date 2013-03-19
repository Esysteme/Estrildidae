<?phpuse glial\synapse\singleton;class forum extends controller{    function index($param)    {		$this->layout_name = "admin";				        if (empty($param))        {            //$tree_id = "1-*-*-*-*-*-*";            $tree_id = "1-1-9-101-438";        } else        {            $tree_id = $param[0];        }        $this->title = __("Forum");        $this->ariane = "> " . $this->title;        /*          0 : pas pris en compte         * : n'mporte quelle valeur         */        $tree = array("id_species_kingdom",            "id_species_phylum",            "id_species_class",            "id_species_order",            "id_species_family",            "id_species_genus",            "id_species_main",            "id_species_sub");        $tab = explode("-", $tree_id);        for ($i = 0; $i < 8; $i++)        {            if (empty($tab[$i]))            {                $tab[$i] = "*";            }        }        $_SQL = singleton::getInstance(SQL_DRIVER);        $sql = "SELECT * FROM forum_category order by disp_position ASC";        $res = $_SQL->sql_query($sql);        $out['category'] = $_SQL->sql_to_array($res);        $i = 0;        $k = 0;        foreach ($out['category'] as $val)        {            $j = 0;            if ($val['id'] != 2)            {                //forum fixe en fonction de la table forum_main                $sql = "SELECT name, description,id FROM forum_main 				WHERE id_forum_category = " . $val['id'] . "";                foreach ($tree as $value)                {                    if ($tab[$k] === "*")                    {                        continue;                    }                    $sql .= " AND " . $tree[$k] . " = " . $tab[$k] . " OR " . $tree[$k] . " = 0 ";                    $k++;                }                $sql .= " order by disp_position ASC";            } else            {                //forum dynamique en fonction des especes                $sql = "select id,scientific_name as name  				FROM " . substr($tree[$k], 3) . " 				WHERE " . $tree[$k - 1] . " = " . $tab[$k - 1] . "				AND is_valid = 1				ORDER BY scientific_name";                $part = explode("_", $tree[$k]);                if ($part[2] == "sub")                    $part[2] = "Subspecies";                if ($part[2] == "main")                    $part[2] = "Species";                $out['category'][$i]['name'] = ucwords($part[2]);            }            //echo $sql."<br />";            $res = $_SQL->sql_query($sql);            $out['forum'][$val['id']] = $_SQL->sql_to_array($res);            $i++;        }        $out['tree_id'] = $tree_id;        $this->set('out', $out);        //debug($out);    }    function view($param)    {        $tree = array("id_species_kingdom",            "id_species_phylum",            "id_species_class",            "id_species_order",            "id_species_family",            "id_species_genus",            "id_species_main",            "id_species_sub");        if (empty($param))        {            die("error : id forum manquante");        } else        {            $tree_id = explode("-", $param[0]);            $id_forum = $tree_id[0];            unset($tree_id[0]);        }        $_SQL = singleton::getInstance(SQL_DRIVER);        $k = count($tree_id);        $sql = "select id,scientific_name as name  		FROM " . substr($tree[$k - 1], 3) . " 		WHERE id = " . $tree_id[$k] . "		AND is_valid = 1		ORDER BY scientific_name";        $res = $_SQL->sql_query($sql);        $ob = $_SQL->sql_fetch_object($res);        if ($id_forum != 0)        {            $sql = "SELECT * FROM forum_main where id = " . $id_forum . "";            $res = $_SQL->sql_query($sql);            $ob2 = $_SQL->sql_fetch_object($res);            $ob->name = $ob->name . " (" . strtolower(__($ob2->name)) . ")";        }        $this->title = $ob->name;        $this->ariane = "> <a href=\"" . LINK . "forum/\">" . __("Forum") . "</a> > " . $this->title;        $sql = "select * FROM forum_topic		WHERE 1";        if ($id_forum != 0)        {            $sql .= " AND id_forum_main = " . $id_forum . " ";        }        unset($tree_id[0]);        $k = 1;        foreach ($tree_id as $id)        {            $sql .= " AND " . $tree[$k] . " = " . $id . " ";            $k++;        }        $res = $_SQL->sql_query($sql);        $out = array();        $out['forum'] = $_SQL->sql_to_array($res);        $out['link'] = $param[0];        $this->set('out', $out);        //echo $sql;    }    function post($param)    {        $this->javascript = array("jquery-1.4.2.min.js");        $this->code_javascript[] = "$('#forum_post-id_language').change(function() {		  $('#flag').removeAttr('class').addClass($('#forum_post-id_language').val());		});";        $tree = array("id_species_kingdom",            "id_species_phylum",            "id_species_class",            "id_species_order",            "id_species_family",            "id_species_genus",            "id_species_main",            "id_species_sub");        if (empty($param))        {            die("error : id forum manquante");        } else        {            $tree_id = explode("-", $param[0]);            $id_forum = $tree_id[0];            unset($tree_id[0]);        }        $_SQL = singleton::getInstance(SQL_DRIVER);        $k = count($tree_id);        $sql = "select id,scientific_name as name  		FROM " . substr($tree[$k - 1], 3) . " 		WHERE id = " . $tree_id[$k] . "		AND is_valid = 1		ORDER BY scientific_name";        $res = $_SQL->sql_query($sql);        $ob = $_SQL->sql_fetch_object($res);        if ($id_forum != 0)        {            $sql = "SELECT * FROM forum_main where id = " . $id_forum . "";            $res = $_SQL->sql_query($sql);            $ob2 = $_SQL->sql_fetch_object($res);            $ob->name = $ob->name . " (" . strtolower(__($ob2->name)) . ")";        }        $_LG = singleton::getInstance("Language");        $lg = explode(",", LANGUAGE_AVAILABLE);        $nbchoice = count($lg);        for ($i = 0; $i < $nbchoice; $i++)        {            $data['geolocalisation_country'][$i]['libelle'] = $_LG->languagesUTF8[$lg[$i]];            $data['geolocalisation_country'][$i]['id'] = $lg[$i];        }        $data['default_lg'] = $_LG->Get();        $this->title = __("Post");        $this->ariane = "> <a href=\"" . LINK . "forum/\">" . __("Forum") . "</a> > <a href=\"" . LINK . "forum/view/" . $param[0] . "\">" . $ob->name . "</a> > " . $this->title;        /*         * *********** */        //if ($id_forum != 0)        //{        $k = 1;        //CONCAT('<b>',name,'</b> - ',description,'')        $sql = "SELECT name as libelle,id FROM forum_main WHERE id_forum_category = 1";        foreach ($tree_id as $value)        {            if ($tree_id[$k] === "*")            {                continue;            }            $sql .= " AND " . $tree[$k] . " = " . $tree_id[$k] . " OR " . $tree[$k] . " = 0 ";            $k++;        }        $sql .= " order by disp_position ASC";        //}        //echo $sql."<br />";        $res = $_SQL->sql_query($sql);        $data['category_default'] = $id_forum;        $data['categories'] = $_SQL->sql_to_array($res);        $this->set("data", $data);    }}?>