<?php




class faq extends controller
{
	
	
	
	public $module_group = "Communication Templates";
	public $method_administration = array("user","roles");
	
	
	function index()
	{
		$this->title = __("FAQ");
		$this->ariane = "> ".$this->title;
		




	}
	
	

}

?>