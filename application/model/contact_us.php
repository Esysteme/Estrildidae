<?php

class contact_us extends sql
{
var $schema = "CREATE TABLE `contact_us` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_geolocalisation_country` int(11) NOT NULL,
  `id_geolocalisation_city` int(11) NOT NULL,
  `message` int(11) NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

var $field = array("id","name","firstname","id_geolocalisation_country","id_geolocalisation_city","message","ip","date","email");

var $validate = array(
	'name' => array(
		'not_empty' => array('This field is requiered.')
	),
	'firstname' => array(
		'not_empty' => array('This field is requiered.')
	),
	'id_geolocalisation_country' => array(
		'reference_to' => array('The constraint to geolocalisation_country.id isn\'t respected.','geolocalisation_country', 'id')
	),
	'id_geolocalisation_city' => array(
		'reference_to' => array('The constraint to geolocalisation_city.id isn\'t respected.','geolocalisation_city', 'id')
	),
	'message' => array(
		'numeric' => array('This must be an int.')
	),
	'ip' => array(
		'ip' => array('your IP is not valid')
	),
	'date' => array(
		'time' => array('This must be a time.')
	),
	'email' => array(
		'email' => array('your email is not valid')
	),
);

function get_validate()
{
return $this->validate;
}
}
