<?php
if ( file_exists( dirname( __FILE__ ) . '/local-config.php')) {
  include( dirname( __FILE__ ) . '/local-config.php' );
}

define('DB_CHARSET',    'utf8');
define('DB_COLLATE',    '');

define('AUTH_KEY',         'pLUvf4GyXHTZHVUPNfbGWN7uhOs4rqWtav3ArXLStL-LZ5nrUCy8DhzbISb5Ds6NmJ01ZzcFnFXMdeYEuq0OGQ');
define('SECURE_AUTH_KEY',  'gjipFUSUNkuDMviJq82PyRYFu1sUqYmrzYFgAQvoEOK_E0lfevHYaFHCauNhEsYHs8Hn3EIUbH0vx_Qbdqyudg');
define('LOGGED_IN_KEY',    '2su0kH21NdCwFMtcoo_u9WRQ7zcdEfqt6uWM4vt8oTHCHOiz3s0oNzFWmTtwoNDvr0etkAC4puA-SFLYEdg1NA');
define('NONCE_KEY',        'huTln6uNilWq5mJjHN0HNsjdskJ99InLJkf03hQmPU_ySk1L2j1QWDP6aWyIBuBc6ez2bTYTPQMpElCDCSnqwg');
define('AUTH_SALT',        'eizf9FiDUFvoJuU_pFWqEaJVHDGCtvD2kq9s0DIaeIH7jitqENAW31pGAUcEiiiARK6Lhq6ndUxbP6_eLd1_lA');
define('SECURE_AUTH_SALT', 'ItVP4cwXFJqo8fbPCLR8PQ_b85PaespE2y9txZVnk2bsBtlbQvypcbtUlolM7M8lP00xQZXkW78pK5KUtQwDXw');
define('LOGGED_IN_SALT',   'x7gipUh1il4mYHIS5XT9fhkKkeGZCpT8Kk4RWYUghOQWmbA_Qmx4VgkHkCugEBmKHOTTnY9gQp9au3JiGsUSbA');
define('NONCE_SALT',       'wX8q-w5bGnZuafR9uoWYzJ-EWxt3bBDIS0mzcElMFVINTosMB3P56NcthVco15zqkMBO0xV0xqKGymCx7dbAag');

$table_prefix  = 'wp_';

define('WPLANG', 'nb_NO');

if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');
