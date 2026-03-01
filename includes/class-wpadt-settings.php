<?php
/**
 * Admin Dark Theme Settings
 *
 * Handles the options page and settings under WordPress Admin > Settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Admin_Dark_Theme_Settings {

	private $default_colors = array(
		'wpadt_bg_base'      => '#2b3641',
		'wpadt_bg_dark'      => '#222b34',
		'wpadt_bg_light'     => '#364452',
		'wpadt_bg_lighter'   => '#425261',
		'wpadt_border'       => '#3e4e5e',
		'wpadt_text_main'    => '#d1dbe5',
		'wpadt_text_heading' => '#f0f5fa',
		'wpadt_link'         => '#66b3e6',
		'wpadt_accent'       => '#20c997',
		'wpadt_editor_text_color' => '#d1dbe5',
		'wpadt_editor_bg_color'   => '#2b3641',
	);

	/**
	 * Reset colors to defaults.
	 */
	public function handle_reset_colors() {
		if ( isset( $_GET['wpadt_reset_colors'], $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpadt_reset' ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				foreach ( $this->default_colors as $key => $val ) {
					delete_option( $key );
				}
				delete_option( 'wpadt_editor_text_size' );
				wp_safe_redirect( admin_url( 'options-general.php?page=wpadt-settings&reset=success' ) );
				exit;
			}
		}
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'handle_reset_colors' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );
	}

	public function enqueue_color_picker( $hook_suffix ) {
		if ( 'settings_page_wpadt-settings' !== $hook_suffix ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wpadt-settings-script', WP_ADMIN_DARK_THEME_PLUGIN_URL . 'assets/js/admin-settings.js', array( 'wp-color-picker' ), WP_ADMIN_DARK_THEME_VERSION, true );
	}

	/**
	 * Register settings logic.
	 */
	public function register_settings() {
		// Colors
		foreach ( $this->default_colors as $key => $default_color ) {
			register_setting( 'wpadt_settings_group', $key, array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_hex_color',
				'default'           => $default_color,
			) );
		}

		register_setting( 'wpadt_settings_group', 'wpadt_editor_text_size', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 16,
		) );

		register_setting( 'wpadt_settings_group', 'wpadt_enable_dark_theme', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '1',
		) );

		add_settings_section(
			'wpadt_general_settings',
			__( 'General Settings', 'wp-admin-dark-theme' ),
			array( $this, 'general_settings_callback' ),
			'wpadt-settings'
		);

		add_settings_field(
			'wpadt_enable_dark_theme',
			__( 'Enable Dark Theme', 'wp-admin-dark-theme' ),
			array( $this, 'render_enable_dark_theme_field' ),
			'wpadt-settings',
			'wpadt_general_settings'
		);

		add_settings_section(
			'wpadt_colors_settings',
			__( 'Custom Theme Colors', 'wp-admin-dark-theme' ),
			array( $this, 'colors_settings_callback' ),
			'wpadt-settings'
		);

		$fields = array(
			'wpadt_bg_base'      => __( 'Base Background Color', 'wp-admin-dark-theme' ),
			'wpadt_bg_dark'      => __( 'Dark Background Color', 'wp-admin-dark-theme' ),
			'wpadt_bg_light'     => __( 'Light Background Color', 'wp-admin-dark-theme' ),
			'wpadt_bg_lighter'   => __( 'Lighter Background Color (Hover states)', 'wp-admin-dark-theme' ),
			'wpadt_border'       => __( 'Border Color', 'wp-admin-dark-theme' ),
			'wpadt_text_main'    => __( 'Main Text Color', 'wp-admin-dark-theme' ),
			'wpadt_text_heading' => __( 'Heading Text Color', 'wp-admin-dark-theme' ),
			'wpadt_link'         => __( 'Link Color', 'wp-admin-dark-theme' ),
			'wpadt_accent'       => __( 'Accent Color (Tabs, active plugins)', 'wp-admin-dark-theme' ),
		);

		foreach ( $fields as $key => $label ) {
			add_settings_field(
				$key,
				$label,
				array( $this, 'render_color_field' ),
				'wpadt-settings',
				'wpadt_colors_settings',
				array( 'key' => $key, 'default' => $this->default_colors[ $key ] )
			);
		}
	}

	/**
	 * General settings section callback.
	 */
	public function general_settings_callback() {
		echo '<p>' . esc_html__( 'Toggle the dark theme globally from here, which is especially useful on mobile devices where the admin bar toggle might be hidden.', 'wp-admin-dark-theme' ) . '</p>';
	}

	/**
	 * Render "Enable Dark Theme" checkbox.
	 */
	public function render_enable_dark_theme_field() {
		$enabled = get_option( 'wpadt_enable_dark_theme', '1' );
		?>
		<input type="hidden" name="wpadt_enable_dark_theme" value="0" />
		<input type="checkbox" name="wpadt_enable_dark_theme" id="wpadt_enable_dark_theme" value="1" <?php checked( '1', $enabled, true ); ?> />
		<label for="wpadt_enable_dark_theme"><?php esc_html_e( 'Dark theme active globally.', 'wp-admin-dark-theme' ); ?></label>
		<?php
	}

	/**
	 * Colors settings section callback.
	 */
	public function colors_settings_callback() {
		echo '<p>' . esc_html__( 'Customize the colors of your dark theme:', 'wp-admin-dark-theme' ) . '</p>';
	}

	/**
	 * Render color field.
	 */
	public function render_color_field( $args ) {
		$key   = $args['key'];
		$value = get_option( $key, $args['default'] );
		?>
		<input type="text" name="<?php echo esc_attr( $key ); ?>" class="wpadt-color-field" value="<?php echo esc_attr( $value ); ?>" data-default-color="<?php echo esc_attr( $args['default'] ); ?>" />
		<?php
	}

	/**
	 * Add the menu page.
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'WP Admin Dark Theme', 'wp-admin-dark-theme' ),
			__( 'Dark Theme', 'wp-admin-dark-theme' ),
			'manage_options',
			'wpadt-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$reset_url = wp_nonce_url( admin_url( 'options-general.php?page=wpadt-settings&wpadt_reset_colors=1' ), 'wpadt_reset' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WP Admin Dark Theme Options', 'wp-admin-dark-theme' ); ?></h1>
			
			<?php if ( isset( $_GET['reset'] ) && 'success' === $_GET['reset'] ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Colors have been reset to defaults.', 'wp-admin-dark-theme' ); ?></p>
				</div>
			<?php endif; ?>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'wpadt_settings_group' );
				do_settings_sections( 'wpadt-settings' );
				?>
				<p class="submit" style="display: flex; align-items: center; gap: 15px;">
					<?php submit_button( __( 'Save Changes', 'wp-admin-dark-theme' ), 'primary', 'submit', false ); ?>
					<a href="<?php echo esc_url( $reset_url ); ?>" class="button button-secondary" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to revert to the default dark theme colors?', 'wp-admin-dark-theme' ); ?>');">
						<?php esc_html_e( 'Reset to Defaults', 'wp-admin-dark-theme' ); ?>
					</a>
				</p>
			</form>
		</div>
		<?php
	}

}

new WP_Admin_Dark_Theme_Settings();
