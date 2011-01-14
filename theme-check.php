<?php
/*
Plugin Name: Theme-Check
Plugin URI: http://pross.org.uk/plugins
Description: A simple and easy way to test your theme for all the latest WordPress standards and practices. A great theme development tool!
Author: Pross
Author URI: http://pross.org.uk
Version: 20101228.1
*/
add_action( 'admin_init', 'tc_i18n' );

function tc_i18n() {
load_plugin_textdomain( 'theme-check', false, dirname( plugin_basename( __FILE__ ) ) );
}

add_action( 'admin_menu', 'themecheck_add_page' );
function themecheck_add_page() {
	add_theme_page( 'Theme Check', 'Theme Check', 'manage_options', 'themecheck', 'themecheck_do_page' );
}

function themecheck_do_page() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.', 'theme-check' ) );
  }

include 'checkbase.php';
include 'main.php';

echo '<div id="theme-check" class="wrap">';
echo '<div id="icon-themes" class="icon32"><br /></div><h2>Theme-Check</h2>';

if ( !isset( $_POST[ 'themename' ] ) ) tc_form();
if ( isset( $_POST[ 'themename' ] ) ) check_main( $_POST[ 'themename' ] );
echo '</div>';
}
