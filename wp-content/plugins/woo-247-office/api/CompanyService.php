<?php

	class CompanyService extends SoapCommon{
		protected $client;

		function __construct(){

			parent::__construct();

			$url = 'https://api.24sevenoffice.com/CRM/Company/V001/CompanyService.asmx?WSDL';

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

			$this->client->__setCookie(SOAP_COOKIE, $_COOKIE[_247_SESSION] );
		}


		function soap_saveCompanies( $company ){
			try{

				$args = array('companies' => array('Company' => $company) );

				$this->debug($args);

				$result = $this->client->SaveCompanies($args);
				$this->debug($result);

				return $result;
			}
			catch (Exception $e){
				$this->debug( $this->client->__getLastRequestHeaders() );
				$this->debug($this->client->__getLastRequest());
				$this->debug($e->getMessage());
			}
		}

		function soap_deleteProducts($args){


		}

		function soap_saveProducts($product){
			try{
				$args = array();
				$args['products']['Product'] = $product;
				$result = $this->client->SaveProducts($args);
				$this->debug($args);
				$this->debug($result);
			}
			catch (Exception $e){
				$this->debug($this->client->__getLastRequest());
				$this->debug($e->getMessage());
			}
		}


		function soap_saveCategories($category){
			try{
				$args = array();
				$args['categories']['Category'] = $category;
				$result = $this->client->SaveCategories($args);
				$this->debug($args);
				$this->debug($result);
			}
			catch (Exception $e){
				$this->debug($this->client->__getLastRequest());
				$this->debug($e->getMessage());
			}
		}


	}
?>