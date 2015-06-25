<?php

	class Authenticate extends SoapCommon{
		protected $client;

		function __construct(){
			parent::__construct();

			$url = 'https://api.24sevenoffice.com/authenticate/v001/authenticate.asmx?WSDL';

			try{
				$this->client  =
					new SoapClient(
						$url,
						array(
							'soap_version' => SOAP_1_1,
							'trace' => true,
							'exceptions' => 1
						)
				);

			}
			catch (Exception $e){
				$this->debug("Webservice 'authenticate' not available");
				return false;
			}
		}


		function soap_hasSession(){
			try{
				$result = $this->client->HasSession();
				// $this->debug($result);
			}
			catch (Exception $e){
				$this->debug($this->client->__getLastRequest());
				$this->debug($e->getMessage());
			}

			return $result->HasSessionResult;
		}

		function soap_Login(){

			try{

				$args = array(
					'credential' => array(
						'ApplicationId' => $this->applicationId,
						'Password' 			=> $this->password,
						'Username' 			=> $this->username,
					)
				 );

				$result = $this->client->Login($args);

				$this->debug($args);
				$this->debug($result);

				if ( $result->LoginResult ){
					Cookie::setCookies( array( _247_SESSION => $result->LoginResult ) );
				}

				return $result->LoginResult;
			}
			catch (Exception $e){
				$this->debug($this->client->__getLastRequest());
				$this->debug($e->getMessage());
			}

		}
	}
?>
