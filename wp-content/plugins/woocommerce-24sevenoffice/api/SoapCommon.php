<?php
	class SoapCommon{
		protected $username;
		protected $password;
		protected $applicationId;
		protected $identityId;

		function __construct(){
			global $api_settings;
			foreach ($api_settings as $key => $value) {
				$this->$key = get_option($key);
			}
		}


		function debug($message = null){
			if( WP_DEBUG === true ){
				error_log( print_r( $message, true ) );
			}
		}

		function isAvailable(){
			if ( isset($this->client) && is_object($this->client) ){
				return true;
			}
			else{
				return false;
			}
		}

	}
?>