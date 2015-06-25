<?php

// add_action( 'woocommerce_order_status_changed',  array('_247_Invoice', 'saveInvoices') , 1, 1 );
// add_action( 'update_order_item_metadata',  array('_247_Invoice', 'saveInvoiceMeta') , 1, 2 );
add_action( 'load-post.php',  array('_247_Invoice', 'updateInvoice') );
add_action( 'save_post',  array('_247_Invoice', 'saveInvoices') , 1, 2 );
add_action( 'woocommerce_delete_order_item',  array('_247_Invoice', 'deleteOrderItem') , 1, 2 );

class _247_Invoice extends InvoiceService{

	protected $id;

	function __construct($id = null){
		$this->id = $id;
		parent::__construct();
	}

	public static function updateInvoice(){
		if ( isset($_GET['post']) && isset($_GET['message']) && $_GET['message'] == 4 ){
			_debug('updateInvoice');
			self::saveInvoices($_GET['post']);
		}
	}

	function getInvoiceRows($products ){
		$invoice_rows = array();

		/*
			Property				Type				Description
			ProductId				Int32				Required for Row Type Normal. Must exist in system. Default value: Int32.MinValue
			RowId						Int32				Used when editing an already existing order. Default value: Int32.MinValue
			VatRate					Decimal			Default value: Decimal.MinValue
			Price						Decimal			Default value: Decimal.MinValue
			Name						String			Default value: “”
			DiscountRate		Decimal 		Default value: Decimal.MinValue
			Quantity				Decimal			Default value: Decimal.MinValue
			Cost						Decimal			Default value: Decimal.MinValue
			InPrice					Decimal			Default value: Decimal.MinValue
			SequenceNumber	Int16				Default value: Int16.MinValue
			Hidden					Boolean			Default value: false. Makes the row hidden on the actual invoice statement.
			Type						RowType			Normal, Text or TextBold. Default value: Normal
			ChangeState			ChangeState	This property must be used when editing an already exisiting order. Default value: ChangeState.None
		*/
		if ( is_array($products) ){
			foreach ($products as $key => $p) {
				$_product = array();

				if ( isset($p['product_id']) && is_numeric($p['product_id']) ){
					$meta = get_post_custom( $p['product_id'] );

					if ( isset($meta['247_id'][0]) ){
						$_product['ProductId'] 	= $meta['247_id'][0];
						$_product['Price'] 			= $p['line_total'];
						$_product['VateRate'] 	= $p['line_tax'];
						$_product['Name'] 			= $p['name'];
						$_product['Quantity'] 	= $p['qty'];
						$_product['Type'] 			= 'Normal';
						$_product['ChangeState']= (isset($p['change_state'])) ? $p['change_state'] : 'None';

						if ( isset($p['row_id']) ){
							$_product['RowId']		= $p['row_id'];
						}

						array_push($invoice_rows, $_product);
					}

				}
				else{
					_debug('getInvoiceRows: no product_id');
					_debug($p);
				}
			}
		}


		return $invoice_rows;
	}


	public static function deleteOrderItem($item_id){
		_debug('delete order item');
		_debug($item_id);

		global $post;
		_debug('order');
		_debug($_REQUEST);

		// to do $this->saveInvoices();
	}

	public static function deletedOrderItems($order_id){

	}


	function getInvoice( $order_id){
 		$Invoice = null;

 		if ( $_247_Id = get_post_meta( $order_id, '247_id', true ) ){
 			$invoices =	$this->soap_getInvoices();

		  if ( isset($invoices->InvoiceOrder) && is_array($invoices->InvoiceOrder) ){
		  	foreach ($invoices->InvoiceOrder as $key => $i) {

		  		if ( $i->OrderId == $order_id ){
		  			$Invoice = $i;
		  			break;
		  		}
		  	}
		  }
 		}

	  return $Invoice;
	}


	function getChangeState($order_id){
		$_247_Id =  get_post_meta( $order_id, '247_id', true );
		if ( $_247_Id && is_numeric($_247_Id) ){
			return 'Edit';
		}
		else{
			return 'Add';
		}
	}


	public static function saveInvoices($order_id, $post = null){
		$post = get_post($order_id);

		if ( $post->post_type == 'shop_order'){
			_debug('SaveInvoices');
			$Invoice = new _247_Invoice($order_id);
			$WC_Order = new WC_Order($order_id);
			$order = array();

			$meta = get_post_custom($order_id );

			if ( isset($meta['_customer_user'][0]) ){

				$Customer = new _247_Customer($meta['_customer_user'][0]);

				// user needs a 24sevenoffice account
				if ( !$Customer->_247_Id ){
					_247_Customer::updateProfile($Customer->ID);
					$Customer = new _247_Customer($Customer->ID);
				}

				$order['OrderId'] 						= $order_id;
				$order['CustomerId'] 					= $Customer->_247_Id;
				$order['CustomerReferenceNo'] = '';
				$order['CustomerName'] 				= $Customer->NickName;
				$order['OrderStatus'] 				= 'Web';
				$order['InvoiceTitle'] 				= $post->post_title;
				$order['InvoiceText'] 				= '';
				// $order['InvoiceID'] 					= ( isset($meta['247_id'][0]) ) ? $meta['247_id'][0] : null;
				$order['OrderTotalIncVat'] 		= ( isset($meta['_order_total'][0]) ) ? FormatUtil::formatDecimal($meta['_order_total'][0]) : null;
				$order['OrderTotalVat'] 			= ( isset($meta['_order_tax'][0]) ) ? FormatUtil::formatDecimal($meta['_order_tax'][0]) : null;
				$order['IncludeVAT'] 					= ( isset($meta['_order_tax'][0]) && $meta['_order_tax'][0] > 0 ) ? true : false;
				$order['DateOrdered'] 				= FormatUtil::formatDate($post->post_date);
				$order['DateInvoiced'] 				= FormatUtil::formatDate($post->post_date);
				$order['Currency'] 						= null;
				$order['Paid'] 								= ( isset($meta['_paid_date'][0]) ) ?  FormatUtil::formatDate($meta['_paid_date'][0]) : null;
				$order['Addresses']						= $Customer->Addresses;
				$order['InvoiceEmailAddress']	= $Customer->EmailAddresses['Invoice']['Value'];

				// $_Invoice: saved at 24office
				$_Invoice = $Invoice->getInvoice($order_id);
				$products = $WC_Order->get_items();

				$change_state = $Invoice->getChangeState($order_id);

				if ( $change_state == 'Edit' ){
					if ( isset($_Invoice->InvoiceRows->InvoiceRow) && is_object($_Invoice->InvoiceRows->InvoiceRow) ){
						$_Invoice->InvoiceRows->InvoiceRow = array( 0 => $_Invoice->InvoiceRows->InvoiceRow );
					}

					if ( isset($_Invoice->InvoiceRows->InvoiceRow) && is_array($_Invoice->InvoiceRows->InvoiceRow) ){

						// check if products change_state is Edit or Delete
						foreach ($_Invoice->InvoiceRows->InvoiceRow as $key => $Row) {
							$delete = true;
							foreach ($products as $key => $product) {
								if ( isset($product['product_id']) && is_numeric($product['product_id']) ){
									$_247_id = get_post_meta($product['product_id'], '247_id', true);
								}
								else{
									_debug('saveInvoices: no product id');
									_debug($product);
								}

								if ( $_247_id == $Row->ProductId ){
									$products[$key]['change_state'] = $change_state;
									$products[$key]['row_id'] = $Row->RowId;
									$delete = false;
									break;
								}
							}

							// add empty product & change_state = 'Delete'
							if ( $delete ){
								$wp_post = _247_Products::findProductBy247Id($Row->ProductId);
								// _debug($wp_post);

								if ( isset($wp_post[0]->ID)){
									$_product = array(
										'product_id' 	=> $wp_post[0]->ID, // wp product id
										'row_id'			=> $Row->RowId,
										'line_total'	=> null,
										'line_tax'		=> null,
										'name'				=> null,
										'qty'					=> null,
										'change_state'=> 'Delete'
									);

									array_push($products, $_product);
								}
							}
						}
					}
				}
				else{
					foreach ($products as $key => $product) {
						$products[$key]['change_state'] = $change_state;
					}
				}

				// _debug($products);
				$order['InvoiceRows']				= $Invoice->getInvoiceRows($products );

				$result = $Invoice->soap_saveInvoices($order);

				if ( isset($result->InvoiceOrder->OrderId) ){
					update_post_meta( $order_id, '247_id', $result->InvoiceOrder->OrderId );
				}
			}
		}
	}
}