<?php
/**
 * Plugin Name: Theme Check
 * Plugin URI: https://github.com/WordPress/theme-check/
 * Description: A simple and easy way to test your theme for all the latest WordPress standards and practices. A great theme development tool!
 * Author: Theme Review Team
 * Version: 20211203
 * Text Domain: theme-check
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

class ThemeCheckMain {
	function __construct() {
		add_action( 'admin_init', array( $this, 'tc_i18n' ) );
		add_action( 'admin_menu', array( $this, 'themecheck_add_page' ) );
	}

	function tc_i18n() {
		load_plugin_textdomain( 'theme-check', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	function load_styles() {
		wp_enqueue_style( 'style', plugins_url( 'assets/style.css', __FILE__ ), array(), '1', 'screen' );
	}

	function themecheck_add_page() {
		$page = add_theme_page( 'Theme Check', 'Theme Check', 'manage_options', 'themecheck', array( $this, 'themecheck_do_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'load_styles' ) );
	}

	function tc_add_headers( $extra_headers ) {
		$extra_headers = array( 'License', 'License URI', 'Template Version' );
		return $extra_headers;
	}

	function themecheck_do_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'theme-check' ) );
		}

		add_filter( 'extra_theme_headers', array( $this, 'tc_add_headers' ) );

		include 'checkbase.php';
		include 'main.php';

		?>
		<div id="theme-check" class="wrap">
		<h1><?php echo esc_html_x( 'Theme Check', 'title of the main page', 'theme-check' ); ?></h1>
		<div class="theme-check">
		<?php
		tc_form();
		if ( ! isset( $_POST['themename'] ) ) {
			tc_intro();
		}

		if ( isset( $_POST['themename'] ) ) {
			check_admin_referer( 'themecheck-nonce' );

			if ( isset( $_POST['trac'] ) ) {
				define( 'TC_TRAC', true );
			}

			wp_raise_memory_limit();

			check_main( wp_unslash( $_POST['themename'] ) );
		}
		?>
		</div> <!-- .theme-check-->
		</div>
		<?php
	}
}
new ThemeCheckMain();
