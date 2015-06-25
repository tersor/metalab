<?php
	class InvoiceService extends SoapCommon{
		protected $client;

		function __construct(){
			parent::__construct();

			$url = 'https://api.24sevenoffice.com/Economy/InvoiceOrder/V001/InvoiceService.asmx?WSDL';

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


		function soap_getInvoices(){
			try{
				$args = array();
				$args['searchParams']['ChangedAfter'] = '2015-01-01';
				$args['invoiceReturnProperties']			= array('OrderId','InvoiceId');
				$args['rowReturnProperties']					= array('ProductId','RowId');

				// $this->debug('$args');
				// $this->debug($args);
				$result = $this->client->GetInvoices($args);
				// $this->debug('$result');
				// $this->debug($result);

				return $result->GetInvoicesResult;
			}
			catch (Exception $e){
				$this->debug($this->client->__getLastRequest());
				$this->debug($args);
				$this->debug($e->getMessage());
			}
		}

		function soap_saveInvoices($invoice){
			try{
				$args = array();
				$args['invoices']['InvoiceOrder'] = $invoice;
				$this->debug('$args');
				$this->debug($args);

				$result = $this->client->SaveInvoices($args);
				$this->debug('$result');
				$this->debug($result);
				return $result->SaveInvoicesResult;
			}
			catch (Exception $e){
				$this->debug($this->client->__getLastRequest());
				$this->debug($args);
				$this->debug($e->getMessage());
			}
		}


	}
?>
