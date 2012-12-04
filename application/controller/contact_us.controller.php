<?php


class contact_us extends controller
{
	
	function index()
	{
		
		if ($_SERVER['REQUEST_METHOD'] == "POST")
		{
			
			
			
			
			
		}
		
		
		
		
		$this->title = __("Contact us");
		$this->ariane = "> ".$this->title;
		
		$_SQL = Singleton::getInstance(SQL_DRIVER);
		
		$this->javascript = array("jquery.1.3.2.js", "jquery.autocomplete.min.js");
		$this->code_javascript[] = '$("#user_main-id_geolocalisation_city-auto").autocomplete("' . LINK . 'user/city/", {
		extraParams: {
			country: function() {return $("#user_main-id_geolocalisation_country").val();}
		},
		mustMatch: true,
		autoFill: true,
		max: 100,
		scrollHeight: 302,
		delay:0
		});
		$("#user_main-id_geolocalisation_city-auto").result(function(event, data, formatted) {
			if (data)
				$("#user_main-id_geolocalisation_city").val(data[1]);
		});
		$("#user_main-id_geolocalisation_country").change( function() 
		{
			$("#user_main-id_geolocalisation_city-auto").val("");
			$("#user_main-id_geolocalisation_city").val("");
		} ); 

		';

		
		$sql = "SELECT id, libelle from geolocalisation_country where libelle != '' order by libelle asc";
		$res = $_SQL->sql_query($sql);
		$this->data['geolocalisation_country'] = $_SQL->sql_to_array($res);

		$this->set('data', $this->data);
	
	}
	
	


}












?>