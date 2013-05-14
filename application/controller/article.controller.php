<?php

class article extends controller
{

	function index()
	{
		$this->layout_name = 'home';

		$this->title = __("Home");
		$this->ariane = " > " . __("The encyclopedia that you can improve !");
	}
	
	function block_article()
	{
		
	}
	
	function detail($param)
	{
		$this->title = __("Experience breeding : [Spermophaga_haematina]");
		$this->ariane = " > " . __("Articles"). " > ".$this->title;
		
	}

}