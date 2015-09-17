<?php

function mblank_scripts() {
  wp_enqueue_style( 'styles', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'mblank_scripts' );

?>