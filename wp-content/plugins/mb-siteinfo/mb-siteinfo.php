<?php
/*
Plugin Name: Siteinfo
Version: 0.1
Description:
Author: Mediebruket AS
Author URI: http://mediebruket.no
*/

include('MBSiteInfo.php');

class RestApi{

  function __construct(){
    add_action( 'rest_api_init', array($this,'registerRoutes')  );
  }


  function registerRoutes(){
    $routes =
      array(
        'siteinfo' => 'getSiteinfo',
      );

    foreach ($routes as $route => $method) {
       register_rest_route( 'wp', '/'.$route,
        array(
          'methods' => 'GET',
          'callback' => array($this, $method),
        )
      );
    }
  }


  function getSiteinfo($data){
    $blog_id = null;

    if ( is_multisite()
        && isset($_GET['multisite'])
          && $_GET['multisite'] == '1' ){

      if ( isset($_GET['blog_id']) && is_numeric($_GET['blog_id']) ){
        $blog_id = $_GET['blog_id'];
      }
      else{
        $blog_id = 1;
      }
    }



    $SiteInfo = new MBSiteInfo( $blog_id );
    // _log($posts);

    if ( $this->checkRequestHeaders() ){
      return $SiteInfo->Export;
    }
    else{
      return null;
    }
  }


  function checkRequestHeaders(){
    $headers = getallheaders();

    $is_header = ( isset($headers['ACCESS_KEY']) && $headers['ACCESS_KEY'] == getenv('ACCESS_KEY') ) ? true : false;
    $is_dev = ( $_SERVER['SERVER_NAME'] == 'sites' or $_SERVER['SERVER_NAME'] == 'localhost' ) ? true : false;
    if ( $is_header or $is_dev ){
      return true;
    }
    else{
      return false;
    }
  }



}

$RestApi = new RestApi();


if (!function_exists('getallheaders')) { 
  function getallheaders() { 
    $headers = ''; 
    foreach ($_SERVER as $name => $value) { 
      if (substr($name, 0, 5) == 'HTTP_') { 
        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
      } 
    } 
    return $headers; 
  } 
} 
if(!function_exists('_log')){
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}