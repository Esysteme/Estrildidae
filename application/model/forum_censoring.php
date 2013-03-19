<?php

namespace application\model;

use glial\synapse\model;

class forum_censoring extends model
{

	var $schema = "CREATE TABLE `forum_censoring` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search_for` varchar(60) NOT NULL DEFAULT '',
  `replace_with` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";
	var $field = array("id", "search_for", "replace_with");
	var $validate = array(
		'search_for' => array(
			'not_empty' => array('This field is requiered.')
		),
		'replace_with' => array(
			'not_empty' => array('This field is requiered.')
		),
	);

	function get_validate()
	{
		return $this->validate;
	}

}
