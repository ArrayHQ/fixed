<?php
/**
 * Easy Digital Downloads Theme Updater
 *
 * @package EDD Sample Theme
 */

// Includes the files needed for the theme updater
if ( !class_exists( 'Array_Theme_Updater_Admin' ) ) {
	include( dirname( __FILE__ ) . '/theme-updater-admin.php' );
}

// The theme version to use in the updater
define( 'FIXED_SL_THEME_VERSION', wp_get_theme( 'fixed' )->get( 'Version' ) );

// Loads the updater classes
$updater = new Array_Theme_Updater_Admin(

	// Config settings
	$config = array(
		'remote_api_url' => 'https://arraythemes.com', // Site where EDD is hosted
		'item_name'      => 'Fixed WordPress Theme', // Name of theme
		'theme_slug'     => 'fixed', // Theme slug
		'version'        => FIXED_SL_THEME_VERSION, // The current version of this theme
		'author'         => 'Array', // The author of this theme
		'download_id'    => '2001', // Optional, used for generating a license renewal link
		'renew_url'      => '' // Optional, allows for a custom license renewal link
	),

	// Strings
	$strings = array(
		'theme-license'             => __( 'Getting Started', 'fixed' ),
		'enter-key'                 => __( 'Enter your theme license key.', 'fixed' ),
		'license-key'               => __( 'Enter your license key', 'fixed' ),
		'license-action'            => __( 'License Action', 'fixed' ),
		'deactivate-license'        => __( 'Deactivate License', 'fixed' ),
		'activate-license'          => __( 'Activate License', 'fixed' ),
		'save-license'              => __( 'Save License', 'fixed' ),
		'status-unknown'            => __( 'License status is unknown.', 'fixed' ),
		'renew'                     => __( 'Renew?', 'fixed' ),
		'unlimited'                 => __( 'unlimited', 'fixed' ),
		'license-key-is-active'     => __( 'Theme updates have been enabled. You will receive a notice on your Themes page when a theme update is available.', 'fixed' ),
		'expires%s'                 => __( 'Your license for Fixed expires %s.', 'fixed' ),
		'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', 'fixed' ),
		'license-key-expired-%s'    => __( 'License key expired %s.', 'fixed' ),
		'license-key-expired'       => __( 'License key has expired.', 'fixed' ),
		'license-keys-do-not-match' => __( 'License keys do not match.', 'fixed' ),
		'license-is-inactive'       => __( 'License is inactive.', 'fixed' ),
		'license-key-is-disabled'   => __( 'License key is disabled.', 'fixed' ),
		'site-is-inactive'          => __( 'Site is inactive.', 'fixed' ),
		'license-status-unknown'    => __( 'License status is unknown.', 'fixed' ),
		'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'fixed' ),
		'update-available'          => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'fixed' )
	)

);