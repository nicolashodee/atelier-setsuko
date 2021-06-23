<?php
/*
 * Plus admin page - plugins subsection
 *
 * @package Cryout Plus
 */

class Cryout_AddonPlugins {

	public function plugin_link( $item, $license_key = '' ) {
		
		$companion_plugins = array(
			'cryout-featured-content',
		);

		$installed_plugins = get_plugins();

		$item['sanitized_plugin'] = $item['name'];

		$actions = array();

		// repo plugin
		if ( ! $item['version'] ) {
			$item['version'] = TGM_Plugin_Activation::$instance->does_plugin_have_update( $item['slug'] );
		}

		// display 'Install' link
		if ( ! isset( $installed_plugins[$item['file_path']] ) ) {

			$url = esc_url( wp_nonce_url(
				add_query_arg(
					array(
							'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
							'plugin'        => urlencode( $item['slug'] ),
							'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
							'plugin_source' => urlencode( $item['source'] ),
							'tgmpa-install' => 'install-plugin',
							'return_url'    => cryout_sanitize_tn(_CRYOUT_THEME_NAME).'-theme',
						),
					TGM_Plugin_Activation::$instance->get_tgmpa_url()
				),
				'tgmpa-install',
				'tgmpa-nonce'
			) );

			if ( in_array( $item['slug'], $companion_plugins ) && empty( $license_key ) ) {
				$actions = array(
					'install' => '<a class="button button-primary button-disabled" title="' . sprintf( esc_attr__( 'A license key is required to install this plugin', 'cryout' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Install', 'cryout' ) . '</a>',
				);
			} else {
				$actions = array(
					'install' => '<a href="' . $url . '" class="button button-primary" title="' . sprintf( esc_attr__( 'Install %s', 'cryout' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Install', 'cryout' ) . '</a>',
				);
			};
		}

		// display 'Activate' link
		elseif ( is_plugin_inactive( $item['file_path'] ) ) {

			$url = esc_url( add_query_arg(
				array(
						'plugin'               => urlencode( $item['slug'] ),
						'plugin_name'          => urlencode( $item['sanitized_plugin'] ),
						'plugin_source'        => urlencode( $item['source'] ),
						'cryout-activate'       => 'activate-plugin',
						'cryout-activate-nonce' => wp_create_nonce( 'cryout-activate' ),
					),
				admin_url( 'themes.php?page='._CRYOUT_THEME_SLUG.'-plus-theme' )
			) );

			$actions = array(
				'activate' => '<a href="' . $url . '" class="button button-primary" title="' . sprintf( esc_attr__( 'Activate %s', 'cryout' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Activate' , 'cryout' ) . '</a>',
			);

		}

		// display 'Update' link
		elseif ( version_compare( $installed_plugins[$item['file_path']]['Version'], $item['version'], '<' ) ) {

			$url = wp_nonce_url(
				add_query_arg(
					array(
						'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
						'plugin'        => urlencode( $item['slug'] ),
						'tgmpa-update'  => 'update-plugin',
						'plugin_source' => urlencode( $item['source'] ),
						'version'       => urlencode( $item['version'] ),
						'return_url'    => cryout_sanitize_tn(_CRYOUT_THEME_NAME).'-theme',
					),
					TGM_Plugin_Activation::$instance->get_tgmpa_url()
				),
				'tgmpa-update',
				'tgmpa-nonce'
			);

			$actions = array(
				'update' => '<a href="' . $url . '" class="button button-primary" title="' . sprintf( esc_attr__( 'Update %s', 'cryout' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Update', 'cryout' ) . '</a>',
			);

		} elseif ( is_plugin_active( $item['file_path'] ) ) {

			$actions = array(
				'deactivate' => sprintf(
					'<a href="%1$s" class="button button-primary" title="Deactivate %2$s">Deactivate</a>',
					esc_url( add_query_arg(
						array(
							'plugin'                 => urlencode( $item['slug'] ),
							'plugin_name'            => urlencode( $item['sanitized_plugin'] ),
							'plugin_source'          => urlencode( $item['source'] ),
							'cryout-deactivate'       => 'deactivate-plugin',
							'cryout-deactivate-nonce' => wp_create_nonce( 'cryout-deactivate' ),
						),
						admin_url( 'themes.php?page='.cryout_sanitize_tnp(_CRYOUT_THEME_NAME).'-plus-theme' )
					) ),
					$item['sanitized_plugin']
				),
			);

		}

		return $actions;

	} // plugin_link()

	public function __construct() {


		$tgmpa             = TGM_Plugin_Activation::$instance;
		$plugins           = TGM_Plugin_Activation::$instance->plugins;
		$view_totals = array(
			'all'      => array(),
			'install'  => array(),
			'update'   => array(),
			'activate' => array(),

		);

		foreach ( $plugins as $slug => $plugin ) {

			if ( $tgmpa->is_plugin_active( $slug ) && false === $tgmpa->does_plugin_have_update( $slug ) ) {
				// skip plugins that are installed, up-to-date and active
				continue;
			} else {
				$view_totals['all'][ $slug ] = $plugin;

				if ( ! $tgmpa->is_plugin_installed( $slug ) ) {
					$view_totals['install'][ $slug ] = $plugin;
				} else {
					if ( false !== $tgmpa->does_plugin_have_update( $slug ) ) {
						$view_totals['update'][ $slug ] = $plugin;
					}

					if ( $tgmpa->can_plugin_activate( $slug ) ) {
						$view_totals['activate'][ $slug ] = $plugin;
					}
				}
			}
		}

		$all_index = $install_index = $update_index = $activate_index = 0;

		foreach ( $view_totals as $type => $count ) {
			$size = sizeof($count);
			if ( $size < 1 ) {
				continue;
			}

			switch ( $type ) {
				case 'all': 	$all_index = $size; 	break;
				case 'install': $install_index = $size;	break;
				case 'update':	$update_index = $size;	break;
				case 'activate':$activate_index = $size;break;
				default:								break;
			}
		}

		$installed_plugins = get_plugins();
		//var_dump( $installed_plugins );
		?>

		<div class="install-plugins">
			<div class="cryout-plugins">
				<?php //var_dump($plugins); 
				foreach ( $plugins as $plugin ) : ?>
					<?php
					$class = '';
					$plugin_status = '';
					$file_path = $plugin['file_path'];
					$plugin_action = $this->plugin_link( $plugin, cryout_plus_get_license_key() );

					if ( is_plugin_active( $file_path ) ) {
						$plugin_status = 'active';
						$class = 'active';
					}
					?>
					<div class="cryout-plugin <?php echo $class; ?>">
						<div class="cryout-theme-wrapper">
							<div class="cryout-theme-screenshot">
								<img src="<?php echo $plugin['image_url']; ?>" alt="" />
								<div class="cryout-plugin-info">
									<?php if ( isset( $installed_plugins[ $plugin['file_path'] ] ) ) : ?>
										<?php printf( __( 'Installed: %1s', 'cryout' ), $installed_plugins[ $plugin['file_path'] ]['Version'] ); ?>
									<?php elseif ( 'bundled' == $plugin['source_type'] ) : ?>
										<?php printf( esc_attr__( 'Available Version: %s', 'cryout' ), $plugin['version'] ); ?>
									<?php endif; ?>
								</div>
                                <?php if ( isset( $plugin['required'] ) && $plugin['required'] ) : ?>
                                    <div class="cryout-plugin-required">
                                        <?php esc_html_e( 'Recommended', 'cryout' ); ?>
                                    </div>
                                <?php endif; ?>
							</div>
							<h3 class="cryout-theme-name">
								<?php if ( 'active' == $plugin_status ) : ?>
									<span><?php printf( __( '<span class="cryout-active-name">Active: </span>%s', 'cryout' ), $plugin['name'] ); ?></span>
								<?php else : ?>
									<?php echo $plugin['name']; ?>
								<?php endif; ?>
							</h3>
							<div class="cryout-theme-actions">
								<?php foreach ( $plugin_action as $action ) { echo $action; } ?>
							</div>
							<?php if ( isset( $plugin_action['update'] ) && $plugin_action['update'] ) : ?>
								<div class="cryout-theme-update">
									<?php _e( 'Update Available', 'cryout' ); ?>
								</div>
							<?php endif; ?>

					</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div> <?php
	} // __construct()

} // Cryout_AddonPlugins class

new Cryout_AddonPlugins;

// FIN