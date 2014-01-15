<?php

namespace Application\Model;
use \Glial\Synapse\Model;

class species_picture_data extends Model
{
var $schema = "CREATE TABLE `species_picture_data` (
  `id` int(11) NOT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

var $field = array("id","data");

var $validate = array(
	'data' => array(
		'not_empty' => array('This field is requiered.')
	),
);

function get_validate()
{
return $this->validate;
}
}
