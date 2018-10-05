<?php
/**
 * Theme updater admin page and functions.
 *
 * @package Fixed
 */

/**
 * Redirect to Getting Started page on theme activation
 */
function fixed_redirect_on_activation() {
	global $pagenow;

	if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {

		wp_redirect( admin_url( "themes.php?page=fixed-license" ) );

	}
}
add_action( 'admin_init', 'fixed_redirect_on_activation' );

/**
 * Load Getting Started styles in the admin
 *
 * since 1.0.0
 */
function fixed_start_load_admin_scripts() {

	// Load styles only on our page
	global $pagenow;
	if( 'themes.php' != $pagenow )
		return;

	/**
	 * Getting Started scripts and styles
	 *
	 * @since 1.0
	 */

	// Getting Started javascript
	wp_enqueue_script( 'fixed-getting-started', get_template_directory_uri() . '/includes/admin/getting-started/getting-started.js', array( 'jquery' ), '1.0.0', true );

	// Getting Started styles
	wp_register_style( 'fixed-getting-started', get_template_directory_uri() . '/includes/admin/getting-started/getting-started.css', false, '1.0.0' );
	wp_enqueue_style( 'fixed-getting-started' );

	// Thickbox
	add_thickbox();
}
add_action( 'admin_enqueue_scripts', 'fixed_start_load_admin_scripts' );

class Array_Theme_Updater_Admin {

	/**
	 * Variables required for the theme updater
	 *
	 * @since 1.0.0
	 * @type string
	 */
	 protected $remote_api_url = null;
	 protected $theme_slug = null;
	 protected $api_slug = null;
	 protected $version = null;
	 protected $author = null;
	 protected $download_id = null;
	 protected $renew_url = null;
	 protected $strings = null;

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	function __construct( $config = array(), $strings = array() ) {

		$config = wp_parse_args( $config, array(
			'remote_api_url' => 'https://arraythemes.com',
			'theme_slug'     => get_template(),
			'api_slug'       => get_template() . '-wordpress-theme',
			'item_name'      => '',
			'license'        => '',
			'version'        => '',
			'author'         => '',
			'download_id'    => '',
			'renew_url'      => ''
		) );

		// Set config arguments
		$this->remote_api_url = $config['remote_api_url'];
		$this->item_name      = $config['item_name'];
		$this->theme_slug     = sanitize_key( $config['theme_slug'] );
		$this->api_slug       = sanitize_key( $config['api_slug'] );
		$this->version        = $config['version'];
		$this->author         = $config['author'];
		$this->download_id    = $config['download_id'];
		$this->renew_url      = $config['renew_url'];

		// Populate version fallback
		if ( '' == $config['version'] ) {
			$theme = wp_get_theme( $this->theme_slug );
			$this->version = $theme->get( 'Version' );
		}

		// Strings passed in from the updater config
		$this->strings = $strings;

		add_action( 'admin_init', array( $this, 'updater' ) );
		add_action( 'admin_init', array( $this, 'register_option' ) );
		add_action( 'admin_init', array( $this, 'license_action' ) );
		add_action( 'admin_menu', array( $this, 'license_menu' ) );
		add_action( 'update_option_' . $this->theme_slug . '_license_key', array( $this, 'activate_license' ), 10, 2 );
		add_filter( 'http_request_args', array( $this, 'disable_wporg_request' ), 5, 2 );

	}

	/**
	 * Creates the updater class.
	 *
	 * since 1.0.0
	 */
	function updater() {

		/* If there is no valid license key status, don't allow updates. */
		if ( get_option( $this->theme_slug . '_license_key_status', false) != 'valid' ) {
			return;
		}

		if ( !class_exists( 'Array_Theme_Updater' ) ) {
			// Load our custom theme updater
			include( dirname( __FILE__ ) . '/theme-updater-class.php' );
		}

		new Array_Theme_Updater(
			array(
				'remote_api_url' => $this->remote_api_url,
				'version'        => $this->version,
				'license'        => trim( get_option( $this->theme_slug . '_license_key' ) ),
				'item_name'      => $this->item_name,
				'author'         => $this->author
			),
			$this->strings
		);
	}

	/**
	 * Adds a menu item for the theme license under the appearance menu.
	 *
	 * since 1.0.0
	 */
	function license_menu() {

		$strings = $this->strings;

		add_theme_page(
			$strings['theme-license'],
			$strings['theme-license'],
			'manage_options',
			$this->theme_slug . '-license',
			array( $this, 'license_page' )
		);
	}

	/**
	 * Outputs the markup used on the theme license page.
	 *
	 * since 1.0.0
	 */
	function license_page() {

		$strings = $this->strings;

		$license = trim( get_option( $this->theme_slug . '_license_key' ) );
		$status = get_option( $this->theme_slug . '_license_key_status', false );

		// Checks license status to display under license key
		if ( ! $license ) {
			$message    = $strings['enter-key'];
		} else {
			// For testing messages
			// delete_transient( $this->theme_slug . '_license_message' );

			if ( ! get_transient( $this->theme_slug . '_license_message', false ) ) {
				set_transient( $this->theme_slug . '_license_message', $this->check_license(), ( 60 * 60 * 24 ) );
			}
			$message = get_transient( $this->theme_slug . '_license_message' );
		}

		/**
		 * Retrieve help file and theme update changelog
		 *
		 * since 1.0.0
		 */

		// Theme info
		$theme = wp_get_theme( 'fixed' );

		// Lowercase theme name for resources links
		$theme_name_lower = get_template();

		// Grab the change log from arraythemes.com for display in the Latest Updates tab
		$changelog = wp_remote_get( 'https://arraythemes.com/themes/' . $this->api_slug . '/changelog/' );
		if( $changelog && !is_wp_error( $changelog ) && 200 === wp_remote_retrieve_response_code( $changelog ) ) {
			$changelog = $changelog['body'];
		} else {
			$changelog = esc_html__( 'There seems to be a temporary problem retrieving the latest updates for this theme. You can always view the latest updates in your Array Dashboard.', 'fixed' );
		}


		/**
		 * Create recommended plugin install URLs
		 *
		 * since 1.0.0
		 */

		if( is_multisite() ) {
			$toolkitUrl = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=array-toolkit&TB_iframe=true&width=640&height=589' );
		} else {
			$toolkitUrl = admin_url( 'plugin-install.php?tab=plugin-information&plugin=array-toolkit&TB_iframe=true&width=640&height=589' );
		}
	?>

			<div class="wrap getting-started">
				<h2 class="notices"></h2>
				<div class="intro-wrap">
					<div class="intro">
						<h3><?php printf( esc_html__( 'Getting started with %1$s', 'fixed' ), $theme['Name'] ); ?></h3>

						<h4><?php printf( esc_html__( 'You will find everything you need to get started with Fixed below.', 'fixed' ), $theme['Name'] ); ?></h4>
					</div>
				</div>

				<div class="panels">
					<ul class="inline-list">
						<li class="current"><a id="help-tab" href="#"><?php esc_html_e( 'Help File', 'fixed' ); ?></a></li>
						<li><a id="updates-tab" href="#"><?php esc_html_e( 'Latest Updates', 'fixed' ); ?></a></li>
						<li><a id="themes-tab" href="#"><?php esc_html_e( 'More Themes & Discounts', 'fixed' ); ?></a></li>
					</ul>

					<div id="panel" class="panel">

						<!-- Help file panel -->
						<div id="help-panel" class="panel-left visible">

							<!-- Grab feed of help file -->
							<?php
								include_once( ABSPATH . WPINC . '/feed.php' );

								$rss = fetch_feed( 'https://arraythemes.com/articles/fixed/feed/?withoutcomments=1' );

								if ( ! is_wp_error( $rss ) ) :
								    $maxitems = $rss->get_item_quantity( 1 );
								    $rss_items = $rss->get_items( 0, $maxitems );
								endif;

								if ( ! is_wp_error( $rss ) ) :
									$rss_items_check = array_filter( $rss_items );
								endif;
							?>

							<!-- Output the feed -->
							<?php if ( is_wp_error( $rss ) || empty( $rss_items_check ) ) : ?>
								<p><?php esc_html_e( 'This help file feed seems to be temporarily down. You can always view the help file on Array in the meantime.', 'fixed' ); ?> <a href="https://arraythemes.com/articles/<?php echo $theme_name_lower; ?>" title="View help file"><?php echo $theme['Name']; ?> <?php esc_html_e( 'Help File &rarr;', 'fixed' ); ?></a></p>
							<?php else : ?>
							    <?php foreach ( $rss_items as $item ) : ?>
									<?php echo $item->get_content(); ?>
							    <?php endforeach; ?>
							<?php endif; ?>
						</div>

						<!-- Updates panel -->
						<div id="updates-panel" class="panel-left">
							<p><?php echo $changelog; ?></p>
						</div><!-- .panel-left updates -->

						<!-- More themes -->
						<div id="themes-panel" class="panel-left">
							<div class="theme-intro">
								<div class="theme-intro-left">
									<p><?php _e( 'Join the Theme Club to download all the themes you see below and new releases for one year for <strong>only <strike>$89</strike> <span>$71.20</span></strong> with the code <strong>THEMEPACK20</strong>!', 'fixed' ); ?></p>
								</div>
								<div class="theme-intro-right">
									<a class="button-primary club-button" href="<?php echo esc_url('https://arraythemes.com/wordpress-themes/?theme_pack'); ?>"><?php esc_html_e( 'Shop Themes Now', 'fixed' ); ?> &rarr;</a>
								</div>
							</div>

							<div class="theme-list">
							<?php
							// @todo cache this after all the dust has settled
							$themes_list = wp_remote_get( 'https://arraythemes.com/feed/themes' );

							if ( ! is_wp_error( $themes_list ) && 200 === wp_remote_retrieve_response_code( $themes_list ) ) {

								echo wp_remote_retrieve_body( $themes_list );
							} else {
								$themes_link = 'https://arraythemes.com/wordpress-themes';
								printf( __( 'This theme feed seems to be temporarily down. Please check back later, or visit our <a href="%s">Themes page on Array</a>.', 'fixed' ), esc_url( $themes_link ) );
							} ?>

							</div><!-- .theme-list -->

							<div class="theme-intro">
								<div class="theme-intro-left">
									<p><?php _e( 'Join the Theme Club to download all the themes you see below and new releases for one year for <strong>only <strike>$89</strike> <span>$71.20</span></strong> with the code <strong>THEMEPACK20</strong>!', 'fixed' ); ?></p>
								</div>
								<div class="theme-intro-right">
									<a class="button-primary club-button" href="<?php echo esc_url('https://arraythemes.com/wordpress-themes/?theme_pack'); ?>"><?php esc_html_e( 'Shop Themes Now', 'fixed' ); ?> &rarr;</a>
								</div>
							</div>
						</div><!-- .panel-left updates -->

						<div class="panel-right">
							<div class="panel-aside panel-club">
								<a href="<?php echo esc_url('https://arraythemes.com/wordpress-themes/?theme_pack'); ?>"><img src="<?php echo get_template_directory_uri() . '/includes/admin/getting-started/club.jpg'; ?>" alt="<?php esc_html_e( 'Join the Theme Club!', 'fixed' ); ?>" /></a>

								<div class="panel-club-inside">
									<h4><?php esc_html_e( 'Instantly download 20+ pixel-perfect WordPress themes!', 'fixed' ); ?></h4>

									<p><?php esc_html_e( 'Join the Theme Club and download our entire collection of responsive themes, new theme releases and get speedy, expert support &mdash; a massive value!', 'fixed' ); ?></p>

									<div class="club-discount">
										<p><strong><?php esc_html_e( 'Exclusive 20% Discount!', 'fixed' ); ?></strong></p>

										<p><?php
											$themes_link = '<code><strong>THEMEPACK20</strong></code>';
											printf( __( 'Use the code %s to get 20&#37; off your next WordPress theme or Theme Club membership!', 'fixed' ), $themes_link );
										?></p>
									</div>

									<a class="button-primary club-button" href="<?php echo esc_url('https://arraythemes.com/wordpress-themes/?theme_pack'); ?>"><?php esc_html_e( 'Shop Themes Now', 'fixed' ); ?> &rarr;</a>
								</div>
							</div>

							<!-- Activate license -->
							<div class="panel-aside">
								<?php if ( 'valid' == $status ) { ?>

								<h4><?php esc_html_e( 'Sweet, your license is active!', 'fixed' ); ?></h4>

								<p><?php esc_html_e( 'You will receive a notice on your Themes page when a theme update is available.', 'fixed' ); ?></p>

								<?php } else { ?>
									<h4><?php esc_html_e( 'Activate your license to enable theme updates!', 'fixed' ); ?></h4>

								<p>
									<?php esc_html_e( 'With an active license, you can get seamless, one-click theme updates to keep your site healthy and happy. ', 'fixed' );

										$license_link = 'https://arraythemes.com/dashboard';
										printf( __( 'Find your license key in your <a target="_blank" href="%s">Array Dashboard</a>.', 'fixed' ), esc_url( $license_link ) );
									?>
								</p>
								<?php } ?>

								<!-- License setting -->
								<form class="enter-license" method="post" action="options.php">
									<?php settings_fields( $this->theme_slug . '-license' ); ?>

									<input id="<?php echo $this->theme_slug; ?>_license_key" name="<?php echo $this->theme_slug; ?>_license_key" type="text" class="regular-text license-key-input" value="<?php echo esc_attr( $license ); ?>" placeholder="<?php echo $strings['license-key']; ?>"/>

									<!-- If we have a license -->
									<?php
										wp_nonce_field( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' );
										if ( 'valid' == $status ) { ?>
											<input type="submit" class="button-primary" name="<?php echo $this->theme_slug; ?>_license_deactivate" value="<?php esc_attr_e( $strings['deactivate-license'] ); ?>"/>
										<?php } else { ?>
											<small style="font-size:12px;"><?php esc_html_e( 'Be sure to activate your license after saving it.', 'fixed' ); ?></small><br/><br/>
											<?php if ( $license ) { ?>
												<input type="submit" class="button-primary" name="<?php echo $this->theme_slug; ?>_license_activate" value="<?php esc_attr_e( $strings['activate-license'] ); ?>"/>
											<?php } else { ?>
												<input type="submit" class="button-primary" name="<?php echo $this->theme_slug; ?>_license_activate" value="<?php esc_attr_e( $strings['save-license'] ); ?>"/>
											<?php } ?>
										<?php }
										?>

								</form><!-- .enter-license -->

							</div><!-- .panel-aside license -->
						</div><!-- .panel-right -->
					</div><!-- .panel -->
				</div><!-- .panels -->
			</div><!-- .getting-started -->

		<?php
	}

	/**
	 * Registers the option used to store the license key in the options table.
	 *
	 * since 1.0.0
	 */
	function register_option() {
		register_setting(
			$this->theme_slug . '-license',
			$this->theme_slug . '_license_key',
			array( $this, 'sanitize_license' )
		);
	}

	/**
	 * Sanitizes the license key.
	 *
	 * since 1.0.0
	 *
	 * @param string $new License key that was submitted.
	 * @return string $new Sanitized license key.
	 */
	function sanitize_license( $new ) {

		$old = get_option( $this->theme_slug . '_license_key' );

		if ( $old && $old != $new ) {
			// New license has been entered, so must reactivate
			delete_option( $this->theme_slug . '_license_key_status' );
			delete_transient( $this->theme_slug . '_license_message' );
		}

		return $new;
	}

	/**
	 * Makes a call to the API.
	 *
	 * @since 1.0.0
	 *
	 * @param array $api_params to be used for wp_remote_get.
	 * @return array $response decoded JSON response.
	 */
	 function get_api_response( $api_params ) {

		 // Call the custom API.
		$response = wp_remote_get(
			esc_url_raw( add_query_arg( $api_params, $this->remote_api_url ) ),
			array( 'timeout' => 15, 'sslverify' => false )
		);

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		return $response;
	 }

	/**
	 * Activates the license key.
	 *
	 * @since 1.0.0
	 */
	function activate_license() {

		$license = trim( get_option( $this->theme_slug . '_license_key' ) );

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name )
		);

		$license_data = $this->get_api_response( $api_params );

		// $response->license will be either "active" or "inactive"
		if ( $license_data && isset( $license_data->license ) ) {
			update_option( $this->theme_slug . '_license_key_status', $license_data->license );
			delete_transient( $this->theme_slug . '_license_message' );

			// Set the Typekit kit ID
			if( 'invalid' != $license_data->license ) {

				// If the Typekit kit ID is missing from the license response, fetch it by other means.
				if( isset( $license_data->typekit_id ) && empty( $license_data->typekit_id ) || ! isset( $license_data->typekit_id ) ) {

					$response = wp_remote_get( 'https://arraythemes.com/themes/'. $this->api_slug .'/array_json_api/typekit_api/?get-typekit-id='. $license );

					$typekit_id = json_decode( wp_remote_retrieve_body( $response ) );

					if( $typekit_id && ! empty( $typekit_id ) ) {
						update_option( 'array_typekit_id', $typekit_id );
					}

				} else {
					update_option( 'array_typekit_id', $license_data->typekit_id );
				}
			}
		}
	}

	/**
	 * Deactivates the license key.
	 *
	 * @since 1.0.0
	 */
	function deactivate_license() {

		// Retrieve the license from the database.
		$license = trim( get_option( $this->theme_slug . '_license_key' ) );

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name )
		);

		$license_data = $this->get_api_response( $api_params );

		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data && ( $license_data->license == 'deactivated' ) ) {
			// Delete license key status
			delete_option( $this->theme_slug . '_license_key_status' );
			// Delete the Typekit ID
			delete_option( 'array_typekit_id' );
			delete_transient( $this->theme_slug . '_license_message' );
		}
	}

	/**
	 * Constructs a renewal link
	 *
	 * @since 1.0.0
	 */
	function get_renewal_link() {

		// If a renewal link was passed in the config, use that
		if ( '' != $this->renew_url ) {
			return $this->renew_url;
		}

		// If download_id was passed in the config, a renewal link can be constructed
		$license_key = trim( get_option( $this->theme_slug . '_license_key', false ) );
		if ( '' != $this->download_id && $license_key ) {
			$url = esc_url( $this->remote_api_url );
			$url .= '/fixed/?edd_license_key=' . $license_key . '&download_id=' . $this->download_id;
			return $url;
		}

		// Otherwise return the remote_api_url
		return $this->remote_api_url;

	}



	/**
	 * Checks if a license action was submitted.
	 *
	 * @since 1.0.0
	 */
	function license_action() {

		if ( isset( $_POST[ $this->theme_slug . '_license_activate' ] ) ) {
			if ( check_admin_referer( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' ) ) {
				$this->activate_license();
			}
		}

		if ( isset( $_POST[$this->theme_slug . '_license_deactivate'] ) ) {
			if ( check_admin_referer( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' ) ) {
				$this->deactivate_license();
			}
		}

	}

	/**
	 * Checks if license is valid and gets expire date.
	 *
	 * @since 1.0.0
	 *
	 * @return string $message License status message.
	 */
	function check_license() {

		$license = trim( get_option( $this->theme_slug . '_license_key' ) );
		$strings = $this->strings;

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name )
		);

		$license_data = $this->get_api_response( $api_params );

		// If response doesn't include license data, return
		if ( !isset( $license_data->license ) ) {
			$message = $strings['status-unknown'];
			return $message;
		}

		// Get expire date
		$expires = false;
		if ( isset( $license_data->expires ) ) {
			$expires = date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires ) );
			$renew_link = '<a href="' . esc_url( $this->get_renewal_link() ) . '" target="_blank">' . $strings['renew'] . '</a>';
		}

		// Get site counts
		$site_count = $license_data->site_count;
		$license_limit = $license_data->license_limit;

		// If unlimited
		if ( 0 == $license_limit ) {
			$license_limit = $strings['unlimited'];
		}

		if ( $license_data->license == 'valid' ) {
			$message = $strings['license-key-is-active'] . ' ';
			if ( $expires ) {
				$message .= sprintf( $strings['expires%s'], $expires ) . ' ';
			}
			if ( $site_count && $license_limit ) {
				//$message .= sprintf( $strings['%1$s/%2$-sites'], $site_count, $license_limit );
			}
		} else if ( $license_data->license == 'expired' ) {
			if ( $expires ) {
				$message = sprintf( $strings['license-key-expired-%s'], $expires );
			} else {
				$message = $strings['license-key-expired'];
			}
			if ( $renew_link ) {
				$message .= ' ' . $renew_link;
			}
		} else if ( $license_data->license == 'invalid' ) {
			$message = $strings['license-keys-do-not-match'];
		} else if ( $license_data->license == 'inactive' ) {
			$message = $strings['license-is-inactive'];
		} else if ( $license_data->license == 'disabled' ) {
			$message = $strings['license-key-is-disabled'];
		} else if ( $license_data->license == 'site_inactive' ) {
			// Site is inactive
			$message = $strings['site-is-inactive'];
		} else {
			$message = $strings['license-status-unknown'];
		}

		return $message;
	}

	/**
	 * Disable requests to wp.org repository for this theme.
	 *
	 * @since 1.0.0
	 */
	function disable_wporg_request( $r, $url ) {

		// If it's not a theme update request, bail.
		if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {
 			return $r;
 		}

 		// Decode the JSON response
 		$themes = json_decode( $r['body']['themes'] );

 		// Remove the active parent and child themes from the check
 		$parent = get_option( 'template' );
 		$child = get_option( 'stylesheet' );
 		unset( $themes->themes->$parent );
 		unset( $themes->themes->$child );

 		// Encode the updated JSON response
 		$r['body']['themes'] = json_encode( $themes );

 		return $r;
	}

}
