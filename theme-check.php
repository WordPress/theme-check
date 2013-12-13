<?php
/*
Plugin Name: Theme Check
Plugin URI: http://pross.org.uk/plugins
Description: A simple and easy way to test your theme for all the latest WordPress standards and practices. A great theme development tool!
Author: Pross, Otto42
Author URI: http://pross.org.uk
Version: 20131213.1
Text Domain: theme-check
*/

add_action( 'admin_init', 'tc_i18n' );

function tc_i18n() {
	load_plugin_textdomain( 'theme-check', false, 'theme-check/lang' );
}

function load_styles() {
	wp_enqueue_style('style', plugins_url( 'style.css', __FILE__ ), null, null, 'screen');
}

add_action( 'admin_menu', 'themecheck_add_page' );
function themecheck_add_page() {
	$page = add_theme_page( 'Theme Check', 'Theme Check', 'manage_options', 'themecheck', 'themecheck_do_page' );
	add_action('admin_print_styles-' . $page, 'load_styles');
}

function tc_add_headers( $extra_headers ) {
	$extra_headers = array( 'License', 'License URI', 'Template Version' );
	return $extra_headers;
}

function themecheck_do_page() {
	if ( !current_user_can( 'manage_options' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.', 'theme-check' ) );
	}

	add_filter( 'extra_theme_headers', 'tc_add_headers' );

	include 'checkbase.php';
	include 'main.php';

	echo '<div id="theme-check" class="wrap">';
	echo '<div id="icon-themes" class="icon32"><br /></div><h2>Theme-Check</h2>';
	echo '<div class="theme-check">';
		tc_form();
	if ( !isset( $_POST[ 'themename' ] ) )  {
		tc_intro();

	}

	if ( isset( $_POST[ 'themename' ] ) ) {
		if ( isset( $_POST[ 'trac' ] ) ) define( 'TC_TRAC', true );
		check_main( $_POST[ 'themename' ] );
	}
	echo '</div> <!-- .theme-check-->';
	echo '</div>';
}
