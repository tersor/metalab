<?php

	Class Cookie{

		/* sets session cookies */
		public static function setCookies ( $session_data = null){
			$response = true;

			if ( $session_data ){
				foreach ( $session_data as $key => $value ){
					setcookie( $key , maybe_serialize($value), 0, "/"  );
				}
			}
			else
				$response = false;

			return $response;
		}

		public static function decode_cookie ( $cookie ){
			return maybe_unserialize( stripslashes ( urldecode($cookie ) ));
		}

		public static function get_cookies( $name = null){
			$user_data = null;

			if ( $name ){
				if ( isset($_COOKIE[$name]) )
					$user_data = self::decode_cookie( $_COOKIE[$name] );
				else
					$user_data = null;
			}
			else{
				if ( isset($_COOKIE[USERNAME]) )
					$user_data['username'] =  self::decode_cookie( $_COOKIE[USERNAME] );

				if ( isset($_COOKIE[CUSTOMER_NR]) )
					$user_data['customer_number'] =  self::decode_cookie( $_COOKIE[CUSTOMER_NR] );
			}

			return $user_data;
		}



		public static function deleteCookies( $name = null ){

			if ( $name ){
				if ( isset($_COOKIE[$name]) ){
					setcookie($name, " ", time()-COOKIE_LIFETIME, '/');
				}
			}
			else{


			}


			return true;
		}


	}


?>