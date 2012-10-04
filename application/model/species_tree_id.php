<?php

class species_tree_id extends sql
{
var $schema = "CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `species_tree_id` AS select `g`.`id` AS `id_species_kingdom`,`f`.`id` AS `id_species_phylum`,`e`.`id` AS `id_species_class`,`d`.`id` AS `id_species_order`,`c`.`id` AS `id_species_family`,`b`.`id` AS `id_species_genus`,`a`.`id` AS `id_species_main` from ((((((`species_main` `a` join `species_genus` `b` on((`a`.`id_species_genus` = `b`.`id`))) join `species_family` `c` on((`b`.`id_species_family` = `c`.`id`))) join `species_order` `d` on((`c`.`id_species_order` = `d`.`id`))) join `species_class` `e` on((`d`.`id_species_class` = `e`.`id`))) join `species_phylum` `f` on((`e`.`id_species_phylum` = `f`.`id`))) join `species_kingdom` `g` on((`f`.`id_species_kingdom` = `g`.`id`)))";

var $field = array("id_species_kingdom","id_species_phylum","id_species_class","id_species_order","id_species_family","id_species_genus","id_species_main");

var $validate = array(
	'id_species_kingdom' => array(
		'reference_to' => array('The constraint to species_kingdom.id isn't respected.','species_kingdom', 'id')
	),
	'id_species_phylum' => array(
		'reference_to' => array('The constraint to species_phylum.id isn't respected.','species_phylum', 'id')
	),
	'id_species_class' => array(
		'reference_to' => array('The constraint to species_class.id isn't respected.','species_class', 'id')
	),
	'id_species_order' => array(
		'reference_to' => array('The constraint to species_order.id isn't respected.','species_order', 'id')
	),
	'id_species_family' => array(
		'reference_to' => array('The constraint to species_family.id isn't respected.','species_family', 'id')
	),
	'id_species_genus' => array(
		'reference_to' => array('The constraint to species_genus.id isn't respected.','species_genus', 'id')
	),
	'id_species_main' => array(
		'reference_to' => array('The constraint to species_main.id isn't respected.','species_main', 'id')
	),
);

function get_validate()
{
return $this->validate;
}
}
