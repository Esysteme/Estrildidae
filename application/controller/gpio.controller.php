<?php



class gpio extends Controller
{
	public $module_group = "Domotique";
	
	
	
	function index()
	{
	
	
	
	}
	
	function admin_group()
	{
	
		//306900052085371394.png"
		$module['picture'] = "administration/group1.gif";
		$module['name'] = __("Group");
		$module['description'] = __("Manage picture importation from flickr's bot");
	
		return $module;
	}
}