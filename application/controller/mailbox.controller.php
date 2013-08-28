<?php
use \glial\synapse\Controller;

class mailbox extends Controller {

	public $module_group = "Users & access management";
	

	function inbox() {
		$_SQL = Singleton::getInstance(SQL_DRIVER);
		
		
		$sql = "SELECT * FROM user_main a
		INNER JOIN geolocalisation_country b ON a.id_geolocalisation_country = b.id
		INNER JOIN geolocalisation_city c ON a.id_geolocalisation_city = c.id
		
where a.id ='" . $GLOBALS['_SQL']->sql_real_escape_string($GLOBALS['_SITE']['IdUser']) . "'";
		$res = $GLOBALS['_SQL']->sql_query($sql);

		$user = $GLOBALS['_SQL']->sql_to_array($res);
		$this->data['user'] = $user[0];

		$this->title = __('Inbox');
		$this->ariane = "> <a href=\"" . LINK . "user/\">" . __("Members") . "</a> > " 
			. '<a href="' . LINK . 'user/'.$GLOBALS['_SITE']['IdUser'].'">'.$this->data['user']['firstname'] . ' ' . $this->data['user']['name'].'</a>'
			. ' > ' . '<a href="' . LINK . 'user/'.$GLOBALS['_SITE']['IdUser'].'">'.__('Mailbox').'</a>'
			. ' > ' .$this->title;

		

		$this->set("data", $this->data);
	}
	
	function sent_mail()
	{
		
	}
	
	function compose()
	{
		
		
	}
	
	function trash()
	{
		
	}


}

