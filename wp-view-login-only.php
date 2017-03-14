<?php
/*
 * Plugin Name: WP View Login Only
 * Plugin URI:
 * Description: If you view a website without log in, WordPress redirect to the login page
 * Author: mel_cha
 * Version: 1.0
 * Text Domain: wp-view-login-only
 */
function vlo_theme_name_script() {
	wp_enqueue_style( 'wp-view-login-only', plugins_url( 'css/wp-view-login-only.css', __FILE__ ), array(), null );
	wp_print_styles();
}
add_action( 'login_enqueue_scripts', 'vlo_theme_name_script' );

function vlo_view_login_only( $content ) {
	global $pagenow;
	if( !is_user_logged_in() && !is_admin() && ( $pagenow != 'wp-login.php' ) && php_sapi_name() !== 'cli' ){
		auth_redirect();
	}
}
add_action( 'init', 'vlo_view_login_only' );

function vlo_plugins_loaded() {
	load_plugin_textdomain( 'wp-view-login-only', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'vlo_plugins_loaded' );

//add menubar options
function vlo_add_menu() {
	add_options_page(
		'WP View Login Only',
		'WP View Login Only',
		'activate_plugins',
		'vlo',
		'vlo_options'
	);
}
add_action( 'admin_menu', 'vlo_add_menu' );

function vlo_options( $message ) {
	if (! current_user_can( 'activate_plugins' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ), 'wp-view-login-only' );
	}
?>

	<div class="wrap">
		<h1><?php echo __( 'WP View Login Only' , 'wp-view-login-only' ); ?></h1>

		<p><?php echo __( 'Enter the text to be displayed on the login page.Default message is " Welcome to this site. Please log in to continue ".' , 'wp-view-login-only' ) ?></p>
		<form action="" id="vlo-menu-form" method="post">
			<?php
			wp_nonce_field( 'vlo-nonce-key', 'vlo-menu' );
			if( esc_textarea( get_option( 'vlo-message-data' ) ) ) :
				$message = get_option( 'vlo-message-data' );
			else :
				$message = __( 'Welcome to this site. Please log in to continue', 'wp-view-login-only' );
			endif;
			?>
			<table class="form-table permalink-structure">
				<tr>
					<th><label for="vlo-message-data"><?php echo __( 'message' , 'wp-view-login-only' ) ?></label></th>
					<td><textarea name="vlo-message-data" id="vlo-message-data" cols="80" rows="10"><?php echo esc_textarea( $message ); ?></textarea></td>
				</tr>
			</table>

			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr( __('Save Changes', 'wp-view-login-only' ) ); ?>"  /></p>  </form>
		</form>
	</div>
<?php
	return $message;
}
?>

<?php
function vlo_add_login_message() {
	if( !get_option( 'vlo-message-data' ) ) :
		$message = __( 'Welcome to this site. Please log in to continue', 'wp-view-login-only' );
	else :
		$message = get_option( 'vlo-message-data' );
	endif;

	return '<p class="message error vlo-login-attention">'.$message.'</p>';
}
add_filter( 'login_message', 'vlo_add_login_message' );

function vlo_init() {
	if( isset( $_POST['vlo-menu'] ) && $_POST['vlo-menu'] ) :
		if( check_admin_referer( 'vlo-nonce-key', 'vlo-menu' ) ) :
			$e = new WP_Error();

			if ( isset( $_POST['vlo-message-data'] ) && $_POST['vlo-message-data'] ) :
				update_option( 'vlo-message-data', $_POST['vlo-message-data'] );
				$e->add( 'error', __( 'saved the message', 'wp-view-login-only' ) );
				set_transient( 'vlo-admin-errors', $e->get_error_messages(), 10 );
			else :
				update_option( 'vlo-message-data', '' );
			endif;

			wp_safe_redirect( menu_page_url( 'vlo-menu', false ) );
		endif;
	endif;
}
add_action( 'admin_init', 'vlo_init' );

function vlo_admin_notices() {
	if( $messages = get_transient( 'vlo-admin-errors' ) ) :
?>
	<div class="updated">
		<ul>
			<?php foreach( $messages as $message ) : ?>
			<li><?php echo esc_html( $message ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php
	endif;
}
add_action( 'admin_notices', 'vlo_admin_notices' );
?>
