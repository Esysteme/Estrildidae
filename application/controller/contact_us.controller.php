<?php

class contact_us extends controller
{

	function index()
	{


		$_SQL = Singleton::getInstance(SQL_DRIVER);



		if ( $_SERVER['REQUEST_METHOD'] == "POST" )
		{
			$contact_us = array();
			$contact_us['contact_us'] = $_POST['contact_us'];



			debug($contact_us);

			if ( ! $GLOBALS['_SQL']->sql_save($contact_us) )
			{
				debug($contact_us);
				debug($_SQL->sql_error());
				die();
			}
			/*
			  if (! $_SQL->sql_save($data))
			  {
			  debug($data);

			  echo ">>>>>>>>>>>>>>>>>>>>";
			  //debug($_SQL->sql_error());

			  die();
			  } */
		}

		$this->title = __("Contact us");
		$this->ariane = "> " . $this->title;



		$this->javascript = array("jquery.1.3.2.js", "jquery.autocomplete.min.js");
		$this->code_javascript[] = '$("#contact_us-id_geolocalisation_city-auto").autocomplete("' . LINK . 'user/city/", {
		extraParams: {
			country: function() {return $("#contact_us-id_geolocalisation_country").val();}
		},
		mustMatch: true,
		autoFill: true,
		max: 100,
		scrollHeight: 302,
		delay:0
		});
		$("#contact_us-id_geolocalisation_city-auto").result(function(event, data, formatted) {
			if (data)
				$("#contact_us-id_geolocalisation_city").val(data[1]);
		});
		$("#contact_us-id_geolocalisation_country").change( function() 
		{
			$("#contact_us-id_geolocalisation_city-auto").val("");
			$("#contact_us-id_geolocalisation_city").val("");
		} ); 

		';


		$sql = "SELECT id, libelle from geolocalisation_country where libelle != '' order by libelle asc";
		$res = $_SQL->sql_query($sql);
		$this->data['geolocalisation_country'] = $_SQL->sql_to_array($res);

		$this->set('data', $this->data);
	}

}