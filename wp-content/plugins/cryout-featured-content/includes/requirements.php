<?php
/**
 * Handles plugin's minimum PHP and WordPress version requirements 
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

class CryoutFeaturedContent_Requirements {
	
	function __construct( $params = array() ){
		if (!empty($params)) 
			foreach ($params as $key => $value)
				$this->$key = $value;
	} // __construct()
	
	/**
	 * Checks if the system requirements are met
	 *
	 * @return bool True if system requirements are met, false if not
	 */
	function requirements_met() {
		global $wp_version;

		// min php version
		if ( version_compare( PHP_VERSION, $this->required_php_version, '<' ) ) {
			return false;
		}

		// min wp version
		if ( version_compare( $wp_version, $this->required_wp_version, '<' ) ) {
			return false;
		}

		// plugin dependency
		/*
		if ( ! is_plugin_active( 'plugin-directory/plugin-file.php' ) ) {
			return false;
		}
		*/

		return true;
	} // requirements_met()

	/**
	 * Prints an error that the system requirements weren't met.
	 */
	function requirements_error() {
		global $wp_version; 
		
		?>

		<div class="error">
			<p><?php printf( __( '%1$s cannot be activated: Your environment doesn\'t meet all of the system requirements listed below:', 'cryout-featured-content' ), '<strong>' . $this->name . '</strong>' ) ?></p>

			<ul class="ul-disc">
				<li>
					<strong><?php printf( __( 'PHP %1$s+', 'cryout-featured-content' ), $this->required_php_version ) ?></strong>
					<em><?php printf( __( '(You\'re running version %1$s)', 'cryout-featured-content' ),  PHP_VERSION ) ?></em>
				</li>

				<li>
					<strong><?php printf( __( 'WordPress %1$s+', 'cryout-featured-content' ), $this->required_wp_version ) ?></strong>
					<em><?php printf( __( '(You\'re running version %1$s)', 'cryout-featured-content' ), esc_html( $wp_version ) ) ?></em>
				</li>

			</ul>

			<p><?php printf( __( 'If you need to upgrade your PHP version you can ask your hosting provider for assistance. If you need help upgrading WordPress you can refer to %1$s.', 'cryout-featured-content' ), sprintf( '<a href="http://codex.wordpress.org/Upgrading_WordPress">%1$s</a>', __( 'the Codex', 'cryout-featured-content' ) ) ) ?></p>
		</div> <?php
		
	} // requirements_error()

} // class CryoutFeaturedContent_Requirements()

// FIN