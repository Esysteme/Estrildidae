<?phpclass search extends controller{    function index()    {        $this->title = __("Search");        $this->ariane = "> " . $this->title;        if (!empty($_POST['google_search']["key_words"]))        {            include_once APP_DIR . DS . "model" . DS . "google_search" . ".php";            $_SQL = Singleton::getInstance(SQL_DRIVER);            $this->data['google_search'] = $_POST['google_search'];            $this->data['google_search']['date'] = date("Y-m-d H:i:s");            $this->data['google_search']['ip'] = $_SERVER['REMOTE_ADDR'];            $this->data['google_search']['referer'] = $_SERVER['REMOTE_ADDR'];            //debug($this->data['google_search']);            //die();            if (!$_SQL->sql_save($this->data))            {                //die("KO sdwghwdfwdfgwfg");                $error = $_SQL->sql_error();                $_SESSION['ERROR'] = array_merge($_SESSION['ERROR'], $error);                set_flash("error", __("Registration error"), __("One or more problem came when you try to register your account, please verify your informations"));                $ret = array();                foreach ($_POST['google_search'] as $var => $val)                {                    $ret[] = "google_search:" . $var . ":" . $val;                }                $param = implode("/", $ret);                header("location: " . LINK . "search/index/" . $param);            } elseif ($_SERVER['REQUEST_METHOD'] == "POST")            {                $ret = array();                foreach ($_POST['google_search'] as $var => $val)                {                    $ret[] = "google_search:" . $var . ":" . $val;                }                $param = implode("/", $ret);                header("location: " . LINK . "search/index/" . $param);            }        }        if (!empty($_GET['google_search']['key_words']))        {            $start = microtime(true);            include LIB . 'google.lib.php';            $_LG = Singleton::getInstance("Language");            $this->data['language'] = $_LG->Get();            $google = new google;            $this->data['search_result'] = $google->search("www.estrildidae.net/" . $this->data['language'] . "/", $_GET['google_search']['key_words']);            $this->data['execution_time'] = round(microtime(true) - $start, 3);            $this->set("data", $this->data);						        }    }}?>