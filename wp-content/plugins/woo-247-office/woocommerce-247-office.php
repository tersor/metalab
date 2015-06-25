<?php
/*
Plugin Name:          Woocommerce 24SevenOffice
Version:              1.0.0
Author:               Mediebruket AS
Author URI:           http://mediebruket.no
Bitbucket Theme URI:  https://bitbucket.org/mediebruket/woo_247_office
Bitbucket Branch:     master
*/

include_once('conf/global.php');
include_once('util/Cookie.php');
include_once('util/FormatUtil.php');
include_once('api/SoapCommon.php');
include_once('api/Authenticate.php');
include_once('api/ProductService.php');
include_once('api/CompanyService.php');
include_once('api/InvoiceService.php');
include_once('module/_247_Options.php');
include_once('module/_247_Authenticate.php');
include_once('module/_247_Products.php');
include_once('module/_247_Customer.php');
include_once('module/_247_Invoice.php');
include_once('backend/Backend.php');

function _debug($message = null){
			if( WP_DEBUG === true ){
				error_log( print_r( $message, true ) );
			}
		}


function webservice_error_notice() {
	$webservices = array ( 'Authenticate', 'CompanyService', 'InvoiceService', 'ProductService' );
	$not_working = array();
	$error = false;

	foreach ($webservices as $key => $value) {
		$ws = new $value();

		if ( !$ws->isAvailable() ){
			array_push($not_working, $value);
			$error = true;
		}
	}

	if ( $error ){
		$class = "error";
		$message = "Webservice(s) are not available: ".implode(',', $not_working);
    echo "<div class=\"$class\"> <p>$message</p></div>";

    $last_error_mail = get_option('ws_error_mail' );

    if ( !$last_error_mail|| $last_error_mail+1*60*10 < time() ){
    	wp_mail( get_option('admin_email' ), '24SevenOffice: webservices not available', $message );
    	update_option( 'ws_error_mail', time() );
    }
	}
}
add_action( 'admin_notices', 'webservice_error_notice' );
?>