<?php

	class ProductService extends SoapCommon{
		protected $client;

		function __construct(){

			parent::__construct();

			$url = 'https://api.24sevenoffice.com/Logistics/Product/V001/ProductService.asmx?WSDL';

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


		function soap_getProducts(){
			try{
				$args = array(
					'searchParams' => array(
						'Id' => null,
						'No' => null,
						'EAN1' => null,
						'Name' => '',
						'Price' => null,
						'DateChanged' => '2010-01-01',

					),
					'returnProperties' => array( 'Id', 'Name', 'CategoryId', 'Price', 'Weight', 'Stock', 'StatusId', 'CategoryId', 'InPrice', 'Cost', 'EAN1', 'Price', 'No', 'DateChanged', 'MinimumStock', 'OrderProposal', 'StockLocation'  ) );

				$this->debug($args);
				$result = $this->client->GetProducts($args);

				if ( $result->GetProductsResult->Product ){
					return $result->GetProductsResult->Product;
				}
				else{
					return null;
				}
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
				return $result;
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
				return $result;
			}
			catch (Exception $e){
				$this->debug($this->client->__getLastRequest());
				$this->debug($e->getMessage());
			}
		}


		function soap_getCategories(){
			try{
				$result = $this->client->GetCategories();
				$this->debug($result);
				if ( isset($result->GetCategoriesResult->Category) && is_array($result->GetCategoriesResult->Category) ){
					return $result->GetCategoriesResult->Category;
				}
			}

			catch (Exception $e){
				$this->debug($this->client->__getLastRequest());
				$this->debug($e->getMessage());
			}
		}


	}
?>