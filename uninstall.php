<?php
/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete options
$wpadt_options = array(
	'wpadt_enable_dark_theme',
	'wpadt_bg_base',
	'wpadt_bg_dark',
	'wpadt_bg_light',
	'wpadt_bg_lighter',
	'wpadt_border',
	'wpadt_text_main',
	'wpadt_text_heading',
	'wpadt_link',
	'wpadt_accent',
	'wpadt_editor_text_color',
	'wpadt_editor_bg_color',
	'wpadt_editor_text_size',
);

foreach ( $wpadt_options as $option ) {
	delete_option( $option );
}
