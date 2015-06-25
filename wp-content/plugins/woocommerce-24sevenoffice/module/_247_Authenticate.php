<?php

class _247_Authenticate extends Authenticate{
	protected $currentSession;

	function __construct(){

		parent::__construct();
		if ( isset($_COOKIE[_247_SESSION]) ){
			$this->setSession( $_COOKIE[_247_SESSION] );
		}
		else{
			$this->setSession( $this->soap_Login() );
		}

		// set session cookie
		$this->client->__setCookie(SOAP_COOKIE, $this->currentSession);

		// check if session exists
		if ( !$this->hasSession() ){
			$this->setSession( $this->soap_Login() ); // login and set session id
			$this->client->__setCookie(SOAP_COOKIE, $this->currentSession); // set new session to soap header
		}
		else{
			// var_dump("has session");
		}



	}

	function hasSession(){
		return $this->soap_hasSession();
	}

	function setSession($session){
		$this->currentSession = $session;
		setcookie( _247_SESSION , $session, 0, "/"  );
	}

}