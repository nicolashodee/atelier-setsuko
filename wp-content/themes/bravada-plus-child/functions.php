<?php 

// enqueue parent theme styling
function bravada_child_styling(){
	wp_enqueue_style( 'bravada-plus', get_template_directory_uri() . '/style.css', array(), _CRYOUT_THEME_VERSION );  // restore parent stylesheet
	wp_enqueue_style( 'bravada-plus-child', get_stylesheet_directory_uri() . '/style.css', array( 'bravada-plus' ), date('YmdHis', filemtime( get_stylesheet_directory() . '/style.css' ) ) ); 		// enqueue child stylesheet
}
add_action( 'wp_enqueue_scripts', 'bravada_child_styling' );


?>