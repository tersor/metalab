<?php

function mblank_scripts() {
  wp_enqueue_style( 'styles', get_template_directory_uri() . '/style.css' );
  wp_enqueue_style( 'feed_css', esc_url_raw( 'http://www.fotogalleriet.no/forms.css' ), array(), null );
}
add_action( 'wp_enqueue_scripts', 'mblank_scripts' );

?>