<?php
/**
 * Plus TGMPA
 *
 * @package Cryout Plus
 */

// action is hooked in Plus->theme_setup()

function cryout_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		array(
			'name'               => 'Cryout Serious Slider', // The plugin name.
			'slug'               => 'cryout-serious-slider', // The plugin slug (typically the folder name).
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '1.0', // If set, the active plugin must be this version or higher.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'image_url'			 => get_template_directory_uri() . '/plus/resources/images/plugins/cryout-serious-slider.jpg'
		),
		array(
			'name'               => 'Cryout Featured Content', // The plugin name.
			'slug'               => 'cryout-featured-content', // The plugin slug (typically the folder name).
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '1.0', // If set, the active plugin must be this version or higher.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'source'			 => 'https://plus.cryout.eu/download/file/' . hash( 'sha256', 'cryout-featured-content.1.1.zip' ) . '/key/' . cryout_plus_get_license_key() . '/' . cryout_sanitize_tn(_CRYOUT_THEME_NAME),
			'external_url'       => 'https://www.cryoutcreations.eu/wordpress-plugins/featured-content/', // If set, overrides default API URL and points to an external URL.
			'image_url'			 => get_template_directory_uri() . '/plus/resources/images/plugins/cryout-featured-content.jpg'
		),
		array(
			'name'               => 'Jetpack by WordPress.com', // The plugin name.
			'slug'               => 'jetpack', // The plugin slug (typically the folder name).
			'required'           => false, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '5.2', // If set, the active plugin must be this version or higher.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'image_url'			 => get_template_directory_uri() . '/plus/resources/images/plugins/jetpack.jpg'
		),
		array(
			'name'               => 'Force Regenerate Thumbnails', // The plugin name.
			'slug'               => 'force-regenerate-thumbnails', // The plugin slug (typically the folder name).
			'required'           => false, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '2.0.0', // If set, the active plugin must be this version or higher.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'image_url'			 => get_template_directory_uri() . '/plus/resources/images/plugins/force-regenerate-thumbnails.jpg'
		),
		array(
			'name'               => 'Team Members', // The plugin name.
			'slug'               => 'team-members', // The plugin slug (typically the folder name).
			'required'           => false, // If false, the plugin is only 'recommended' instead of required.
			//'version'            => '', // If set, the active plugin must be this version or higher.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'image_url'			 => get_template_directory_uri() . '/plus/resources/images/plugins/team-members.jpg'
		),
		array(
			'name'               => 'Contact Form 7', // The plugin name.
			'slug'               => 'contact-form-7', // The plugin slug (typically the folder name).
			'required'           => false, // If false, the plugin is only 'recommended' instead of required.
			//'version'            => '', // If set, the active plugin must be this version or higher.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'image_url'			 => get_template_directory_uri() . '/plus/resources/images/plugins/contact-form-7.jpg'
		),
		array(
			'name'               => 'Yoast Seo', // The plugin name.
			'slug'               => 'wordpress-seo', // The plugin slug (typically the folder name).
			'required'           => false, // If false, the plugin is only 'recommended' instead of required.
			//'version'            => '', // If set, the active plugin must be this version or higher.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'image_url'			 => get_template_directory_uri() . '/plus/resources/images/plugins/yoast-seo.jpg'
		),

	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 */
	$config = array(
		'id'           => 'cryout',                // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => _CRYOUT_THEME_SLUG . '-addons',		   // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                    // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table

		'strings'      => array(
			'page_title'                      => sprintf( __( '%s Companion Plugins', 'cryout' ), cryout_sanitize_tnl(_CRYOUT_THEME_NAME) ),
			'menu_title'                      => sprintf( __( '%s Plugins', 'cryout' ), cryout_sanitize_tnl(_CRYOUT_THEME_NAME) ),
			/* translators: %s: plugin name. */
			'installing'                      => __( 'Installing Plugin: %s', 'cryout' ),
			/* translators: %s: plugin name. */
			'updating'                        => __( 'Updating Plugin: %s', 'cryout' ),
			'oops'                            => __( 'Something went wrong with the plugin API.', 'cryout' ),
			'notice_can_install_required'     => _n_noop(
				/* translators: 1: plugin name(s). */
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				'cryout'
			),
			'notice_can_install_recommended'  => _n_noop(
				/* translators: 1: plugin name(s). */
				'This theme suggests the following plugin: %1$s.',
				'This theme suggests the following plugins: %1$s.',
				'cryout'
			),
			'notice_ask_to_update'            => _n_noop(
				/* translators: 1: plugin name(s). */
				'The following plugin should be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins should be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				'cryout'
			),
			'notice_ask_to_update_maybe'      => _n_noop(
				/* translators: 1: plugin name(s). */
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'cryout'
			),
			'notice_can_activate_required'    => _n_noop(
				/* translators: 1: plugin name(s). */
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'cryout'
			),
			'notice_can_activate_recommended' => _n_noop(
				/* translators: 1: plugin name(s). */
				'The following suggested plugin is currently inactive: %1$s.',
				'The following suggested plugins are currently inactive: %1$s.',
				'cryout'
			),
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'cryout'
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'cryout'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'cryout'
			),
			'return'                          => __( 'Return to Companion Plugins Installer', 'cryout' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'cryout' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'cryout' ),
			/* translators: 1: plugin name. */
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'cryout' ),
			/* translators: 1: plugin name. */
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'cryout' ),
			/* translators: 1: dashboard link. */
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'cryout' ),
			'dismiss'                         => __( 'Dismiss this notice', 'cryout' ),
			'notice_cannot_install_activate'  => __( 'There are one or more recommended or suggested plugins to install, update or activate.', 'cryout' ),
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'cryout' ),

			'nag_type'                        => 'notice-info', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
		),

	);
	
	tgmpa( $plugins, $config );
	
} // cryout_register_required_plugins()

// FIN
