<?php
/**
 * Class SampleTest
 *
 * @package Wp_View_Login_Only
 */

/**
 * Test case.
 */
class VloTest extends WP_UnitTestCase {

	/**
	 * Show default message test.
	 */
	function test_show_default_message() {
		$result = vlo_add_login_message();
		$msg = '<p class="message error vlo-login-attention">Welcome to this site. Please log in to continue</p>';
		// Replace this with some actual testing code.
		$this->assertEquals( $result, $msg );
	}

	/**
	 * Create default message test.
	 */
	function test_create_default_message() {
		$result = vlo_create_login_message( false );
		$msg = '<p class="message error vlo-login-attention">Welcome to this site. Please log in to continue</p>';
		// Replace this with some actual testing code.
		$this->assertEquals( $result, $msg );
	}

	/**
	 * Custom message test.
	 */
	function test_custom_message() {
		$result = vlo_create_login_message( 'test' );
		$msg = '<p class="message error vlo-login-attention">test</p>';
		// Replace this with some actual testing code.
		$this->assertEquals( $result, $msg );
	}
}
