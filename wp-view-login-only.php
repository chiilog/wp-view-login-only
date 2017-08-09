<?php
/**
 * Plugin Name: WP View Login Only
 * Plugin URI: https://github.com/chiilog/wp-view-login-only
 * Description: If you view a website without log in, WordPress redirect to the login page
 * Author: mel_cha
 * Author URI: https://chiilog.com/
 * Version: 1.2.4
 * Text Domain: wp-view-login-only
 *
 * @package wp-view-login-only
 */

/**
 * Plugin's actions
 */
function vlo_init() {
	add_action( 'init', 'vlo_view_login_only' );
	add_action( 'init', 'vlo_plugins_loaded' );
	add_action( 'admin_menu', 'vlo_add_menu' );
	add_action( 'admin_init', 'vlo_save_options' );
}
vlo_init();

/**
 * Pattern of not redirect
 */
function vlo_view_login_only() {
	global $pagenow;
	if ( ! is_user_logged_in() && ! is_admin() && ( 'wp-login.php' !== $pagenow ) && 'cli' !== php_sapi_name() ) {
		auth_redirect();
	}
}

/**
 * Load translation
 */
function vlo_plugins_loaded() {
	load_plugin_textdomain( 'wp-view-login-only', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Add menubar options
 */
function vlo_add_menu() {
	add_options_page(
		'WP View Login Only',
		'WP View Login Only',
		'activate_plugins',
		'vlo',
		'vlo_options'
	);
}

/**
 * Option page contents
 */
function vlo_options() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ), 'wp-view-login-only' );
	}
?>

	<div class="wrap">
		<h1><?php esc_html_e( 'WP View Login Only' , 'wp-view-login-only' ); ?></h1>

		<p><?php esc_html_e( 'Enter the text to be displayed on the login page.Default message is " Welcome to this site. Please log in to continue ".' , 'wp-view-login-only' ); ?></p>
		<form action="" id="vlo-menu-form" method="post">
			<?php
			wp_nonce_field( 'vlo_nonce_key', 'vlo_menu' );
			if ( esc_textarea( get_option( 'vlo_message_data' ) ) ) {
				$message = get_option( 'vlo_message_data' );
			} else {
				$message = __( 'Welcome to this site. Please log in to continue', 'wp-view-login-only' );
			};
			?>
			<table class="form-table permalink-structure">
				<tr>
					<th><label for="vlo-message-data"><?php esc_html_e( 'message' , 'wp-view-login-only' ); ?></label></th>
					<td><textarea name="vlo_message_data" id="vlo-message-data" cols="80" rows="10"><?php echo esc_textarea( $message ); ?></textarea></td>
				</tr>
			</table>

			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'wp-view-login-only' ); ?>"></p>
		</form>
	</div>
<?php
}

/**
 * Get message in loginpage
 */
function vlo_get_login_message() {
	return get_option( 'vlo_message_data' );
}

/**
 * Create message in loginpage
 *
 * @param string $option showing custom message.
 */
function vlo_create_login_message( $option ) {
	if ( ! $option ) {
		$message = __( 'Welcome to this site. Please log in to continue', 'wp-view-login-only' );
	} else {
		$message = $option;
	}

	return '<p class="message error vlo-login-attention">' . esc_html( $message ) . '</p>';
}

/**
 * Return login message
 */
function vlo_add_login_message() {
	$option = vlo_get_login_message();
	$message = vlo_create_login_message( $option );

	return $message;
}
add_filter( 'login_message', 'vlo_add_login_message' );

/**
 * Save options
 */
function vlo_save_options() {
	$vlomenu = filter_input( INPUT_POST, 'vlo_menu' );
	if ( ! empty( $vlomenu ) ) {
		if ( check_admin_referer( 'vlo_nonce_key', 'vlo_menu' ) ) {
			$data = filter_input( INPUT_POST, 'vlo_message_data' );
			if ( ! empty( $data ) ) {
				update_option( 'vlo_message_data', sanitize_text_field( wp_unslash( $data ) ) );
			} else {
				update_option( 'vlo_message_data', '' );
			};
		};

		add_action( 'admin_notices', 'vlo_admin_notices' );
		wp_safe_redirect( menu_page_url( 'vlo_menu', false ) );
	};
}

/**
 * Show notice
 */
function vlo_admin_notices() {
?>
	<div class="updated">
		<ul>
			<li><?php esc_html_e( 'Saved the message.', 'wp-view-login-only' ); ?></li>
		</ul>
	</div>
<?php
}
