<?php
/**
 * Plugin Name:       WP Admin Dark Theme
 * Description:       Turns the WordPress admin panel into a dark theme. Options for more customization.
 * Version:           1.0.0
 * Author:            Sajjad Amin
 * Author URI:        https://sajjadamin.com
 * Plugin URI:        https://sajjadamin.com
 * Text Domain:       wp-admin-dark-theme
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants
define( 'WP_ADMIN_DARK_THEME_VERSION', '1.0.0' );
define( 'WP_ADMIN_DARK_THEME_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_ADMIN_DARK_THEME_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Initialize the plugin.
 */
function wpadt_init() {
	// Add settings page in the future if needed
	require_once WP_ADMIN_DARK_THEME_PLUGIN_DIR . 'includes/class-wpadt-settings.php';
}
add_action( 'plugins_loaded', 'wpadt_init' );

/**
 * Enqueue the dark theme CSS and JS for the admin area.
 */
function wpadt_enqueue_admin_styles() {
	// Only enqueue if we are in the admin dashboard and not the customizer, for example.
	if ( is_admin() ) {
		// Check if dark theme is currently enabled in settings (default '1')
		$enabled = ( get_option( 'wpadt_enable_dark_theme', '1' ) === '1' );

		if ( $enabled ) {
			// Enqueue dark theme core styles
			wp_enqueue_style(
				'wpadt-admin-dark-theme-core',
				WP_ADMIN_DARK_THEME_PLUGIN_URL . 'assets/css/admin-dark-theme.css',
				array(),
				WP_ADMIN_DARK_THEME_VERSION,
				'all'
			);

			// Inject custom colors if set
			$custom_css = ":root {\n";
			$colors = array(
				'wpadt_bg_base', 'wpadt_bg_dark', 'wpadt_bg_light', 'wpadt_bg_lighter',
				'wpadt_border', 'wpadt_text_main', 'wpadt_text_heading', 'wpadt_link',
				'wpadt_accent'
			);
			foreach ( $colors as $color_key ) {
				$val = get_option( $color_key );
				if ( $val ) {
					// Correctly form the variable by removing wpadt_ first, then replacing _ with -
					$css_var = '--adt-' . str_replace( '_', '-', str_replace( 'wpadt_', '', $color_key ) );
					$custom_css .= "    {$css_var}: " . sanitize_hex_color( $val ) . " !important;\n";
					if ( 'wpadt_accent' === $color_key ) {
					    // Create a darker active bg variant using the accent color for active items
						// For simplicity, we can just map it directly or set an opacity
					    $custom_css .= "    --adt-bg-active: " . sanitize_hex_color( $val ) . "33;\n"; // 33 is ~20% opacity in hex
					}
				}
			}
			$custom_css .= "}\n";

			wp_add_inline_style( 'wpadt-admin-dark-theme-core', $custom_css );
		}

		// Enqueue the JS to handle the toggle
		wp_enqueue_script(
			'wpadt-admin-dark-theme-js',
			WP_ADMIN_DARK_THEME_PLUGIN_URL . 'assets/js/admin-dark-theme.js',
			array( 'jquery' ),
			WP_ADMIN_DARK_THEME_VERSION,
			true
		);

		// Localize script for AJAX
		wp_localize_script( 'wpadt-admin-dark-theme-js', 'wpadtSettings', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'wpadt_toggle_theme_nonce' ),
			'editor_text_size'  => get_option( 'wpadt_editor_text_size', 16 ),
			'editor_text_color' => get_option( 'wpadt_editor_text_color', '#d1dbe5' ),
			'editor_bg_color'   => get_option( 'wpadt_editor_bg_color', '#2b3641' ),
		) );
	}
}
add_action( 'admin_enqueue_scripts', 'wpadt_enqueue_admin_styles', 999 ); // High priority to override default styles

/**
 * Add dark theme CSS to TinyMCE visual editor
 *
 * @param string $mce_css Comma-separated list of CSS files.
 * @return string
 */
function wpadt_tinymce_dark_theme( $mce_css ) {
	$enabled = ( get_option( 'wpadt_enable_dark_theme', '1' ) === '1' );
	if ( $enabled ) {
		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}
		$mce_css .= WP_ADMIN_DARK_THEME_PLUGIN_URL . 'assets/css/editor-dark-theme.css';
	}
	return $mce_css;
}
add_filter( 'mce_css', 'wpadt_tinymce_dark_theme' );

/**
 * Register TinyMCE plugin JS file.
 */
function wpadt_add_tinymce_plugin( $plugin_array ) {
	$enabled = ( get_option( 'wpadt_enable_dark_theme', '1' ) === '1' );
	if ( $enabled ) {
		$ver = filemtime( WP_ADMIN_DARK_THEME_PLUGIN_DIR . 'assets/js/tinymce-wpadt-plugin.js' );
		$plugin_array['wpadttoggle'] = WP_ADMIN_DARK_THEME_PLUGIN_URL . 'assets/js/tinymce-wpadt-plugin.js?v=' . $ver;
	}
	return $plugin_array;
}
add_filter( 'mce_external_plugins', 'wpadt_add_tinymce_plugin' );

/**
 * Register TinyMCE button icon.
 */
function wpadt_register_tinymce_button( $buttons ) {
	$enabled = ( get_option( 'wpadt_enable_dark_theme', '1' ) === '1' );
	if ( $enabled ) {
		array_push( $buttons, 'wpadttoggle' );
	}
	return $buttons;
}
add_filter( 'mce_buttons', 'wpadt_register_tinymce_button' );

/**
 * Apply working-mode text size and colors inside iframe directly via init array.
 */
function wpadt_tinymce_before_init( $mceInit ) {
	$enabled = ( get_option( 'wpadt_enable_dark_theme', '1' ) === '1' );
	if ( ! $enabled ) {
		return $mceInit;
	}

	$size  = get_option( 'wpadt_editor_text_size', 16 );
	$color = get_option( 'wpadt_editor_text_color', '#d1dbe5' );
	$bg    = get_option( 'wpadt_editor_bg_color', '#2b3641' );

	if ( is_numeric( $size ) ) {
		$size .= 'px';
	}

	$custom_css = "body#tinymce.wp-editor { font-size: {$size} !important; color: {$color} !important; background-color: {$bg} !important; }";

	if ( isset( $mceInit['content_style'] ) ) {
		$mceInit['content_style'] .= ' ' . $custom_css;
	} else {
		$mceInit['content_style'] = $custom_css;
	}

	return $mceInit;
}
add_filter( 'tiny_mce_before_init', 'wpadt_tinymce_before_init' );

/**
 * Add toggle button to admin bar.
 */
function wpadt_admin_bar_menu( $wp_admin_bar ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$enabled = ( get_option( 'wpadt_enable_dark_theme', '1' ) === '1' );
	$title   = $enabled ? __( 'Light Mode', 'wp-admin-dark-theme' ) : __( 'Dark Mode', 'wp-admin-dark-theme' );
	$icon    = $enabled ? 'dashicons-lightbulb' : 'dashicons-moon';

	$wp_admin_bar->add_node( array(
		'id'    => 'wpadt-toggle',
		'title' => '<span class="ab-icon dashicons ' . esc_attr( $icon ) . '"></span><span class="ab-label">' . esc_html( $title ) . '</span>',
		'href'  => '#',
		'meta'  => array(
			'title' => __( 'Toggle Admin Theme', 'wp-admin-dark-theme' ),
		),
	) );
}
add_action( 'admin_bar_menu', 'wpadt_admin_bar_menu', 100 );

/**
 * Handle AJAX request to toggle the theme.
 */
function wpadt_ajax_toggle_theme() {
	check_ajax_referer( 'wpadt_toggle_theme_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Permission denied' );
	}

	$enabled = ( get_option( 'wpadt_enable_dark_theme', '1' ) === '1' );
	$new_val = $enabled ? '0' : '1';
	update_option( 'wpadt_enable_dark_theme', $new_val );

	wp_send_json_success( array( 'enabled' => ( $new_val === '1' ) ) );
}
add_action( 'wp_ajax_wpadt_toggle_theme', 'wpadt_ajax_toggle_theme' );

/**
 * Handle AJAX request to save editor UI settings from TinyMCE plugin.
 */
function wpadt_ajax_save_editor_settings() {
	check_ajax_referer( 'wpadt_toggle_theme_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Permission denied' );
	}

	if ( isset( $_POST['size'] ) ) {
		update_option( 'wpadt_editor_text_size', absint( $_POST['size'] ) );
	}
	if ( isset( $_POST['text_color'] ) ) {
		update_option( 'wpadt_editor_text_color', sanitize_hex_color( $_POST['text_color'] ) );
	}
	if ( isset( $_POST['bg_color'] ) ) {
		update_option( 'wpadt_editor_bg_color', sanitize_hex_color( $_POST['bg_color'] ) );
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_wpadt_save_editor_settings', 'wpadt_ajax_save_editor_settings' );

// Future features setup
// Options for customizing colors, custom logo, etc. can go through `class-wpadt-settings.php`.
