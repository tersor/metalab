<?php

add_action( 'profile_update', array('_247_Customer', 'updateProfile') );
add_action( 'user_register', array('_247_Customer', 'updateProfile') );

class _247_Customer extends  CompanyService{
	public $DateCeated;
	public $ID;
	public $_247_Id;
	public $FirstName;
	public $Name;
	public $NickName;
	public $Addresses;
	public $PhoneNumbers;
	public $EmailAddresses;
	public $Country;
	public $Type;

	function __construct( $user_id = null){
		parent::__construct();

		if ( $user_id ){
			$usermeta = get_user_meta($user_id);
			$user = get_userdata($user_id);
			$this->DateCreated = date('Y-m-d', strtotime($user->data->user_registered) );

			$this->ID = $user_id;

			$this->_247_Id 		= $this->setValue( $usermeta, '247_id' );

			$this->FirstName	= $this->setValue( $usermeta, 'shipping_first_name' );
			$this->Name				= $this->setValue( $usermeta, 'shipping_last_name' );
			$this->NickName		= $this->FirstName." ".$this->Name;


			$this->EmailAddresses['Invoice']['Value']	= $user->data->user_email;

			// Phone numbers
			$this->PhoneNumbers['Mobile']['Value']			= $this->setValue( $usermeta, 'billing_phone' );

			// delivery address
			$this->Addresses['Delivery']['Street']			= $usermeta['shipping_address_1'][0]." ".$usermeta['shipping_address_2'][0];
			$this->Addresses['Delivery']['State']				= $this->setValue( $usermeta, 'shipping_state' );
			$this->Addresses['Delivery']['PostalCode']	= $this->setValue( $usermeta, 'shipping_postcode');
			$this->Addresses['Delivery']['City']				= $this->setValue( $usermeta, 'shipping_city');
			$this->Addresses['Delivery']['Country']			= $this->setValue( $usermeta, 'shipping_country');

			// Billing address
			$this->Addresses['Invoice']['Street']				= $usermeta['billing_address_1'][0]." ".$usermeta['billing_address_2'][0];
			$this->Addresses['Invoice']['State']				= $this->setValue( $usermeta, 'billing_state' );
			$this->Addresses['Invoice']['PostalCode']		= $this->setValue( $usermeta, 'billing_postcode');
			$this->Addresses['Invoice']['City']					= $this->setValue( $usermeta, 'billing_city');
			$this->Addresses['Invoice']['Country']			= $this->setValue( $usermeta, 'billing_country');

			$this->Country			= "NO";
			$this->Type			= "Consumer";
		}
	}

	function getProducts(){
		$this->soap_getProducts();
	}

	public static function updateProfile($user_id){

		$user = get_userdata($user_id);
		$usermeta = get_user_meta($user_id);
		$Customer = new _247_Customer($user_id);
		$args = array();

		// Names
		$args['FirstName']	= $Customer->setValue( $usermeta, 'shipping_first_name' );
		$args['Name']				= $Customer->setValue( $usermeta, 'shipping_last_name' );
		$args['NickName']		= $args['FirstName']." ".$args['Name'];

		// various

		if ( $_247_id = get_user_meta($user_id, '247_id', true) ){
			$args['Id'] = $_247_id;
		}

		$args['DateCreated']	= date('Y-m-d', strtotime($user->data->user_registered) );

		// E-mail addreses
		$args['EmailAddresses']['Invoice']['Value']	= $user->data->user_email;
		// $args['EmailAddresses']['Primary']['Value']	= $user->data->user_email;

		// Phone numbers
		$args['PhoneNumbers']['Mobile']['Value']			= $Customer->setValue( $usermeta, 'billing_phone' );

		// Delivery address
		$args['Addresses']['Delivery']['Street']			= $usermeta['shipping_address_1'][0]." ".$usermeta['shipping_address_2'][0];
		$args['Addresses']['Delivery']['State']				= $Customer->setValue( $usermeta, 'shipping_state' );
		$args['Addresses']['Delivery']['PostalCode']	= $Customer->setValue( $usermeta, 'shipping_postcode');
		$args['Addresses']['Delivery']['City']				= $Customer->setValue( $usermeta, 'shipping_city');
		$args['Addresses']['Delivery']['Country']			= $Customer->setValue( $usermeta, 'shipping_country');

		// Billing address
		$args['Addresses']['Invoice']['Street']				= $usermeta['billing_address_1'][0]." ".$usermeta['billing_address_2'][0];
		$args['Addresses']['Invoice']['State']				= $Customer->setValue( $usermeta, 'billing_state' );
		$args['Addresses']['Invoice']['PostalCode']		= $Customer->setValue( $usermeta, 'billing_postcode');
		$args['Addresses']['Invoice']['City']					= $Customer->setValue( $usermeta, 'billing_city');
		$args['Addresses']['Invoice']['Country']			= $Customer->setValue( $usermeta, 'billing_country');

		$args['Country']			= "NO";
		$args['Type']			= "Consumer";

		$result = $Customer->soap_saveCompanies($args);
		if ( isset($result->SaveCompaniesResult->Company->Id) ){
			update_user_meta( $user_id, '247_id', $result->SaveCompaniesResult->Company->Id );
		}
	}


	function setValue( $array, $index){
		if ( isset($array[$index]) ){
			return $array[$index][0];
		}
		else{
			return null;
		}
	}


}