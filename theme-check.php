<?php
/*
Plugin Name: Theme-Check
Plugin URI: http://pross.org.uk/plugins
Description: Run checks on the current theme before uploading to wordpress.
Author: Pross
Author URI: http://pross.org.uk
Version: 20101118.7
*/

add_action( 'admin_menu', 'themecheck_add_page' );
function themecheck_add_page() {
	add_theme_page( 'Theme Check', 'Theme Check', 'manage_options', 'themecheck', 'themecheck_do_page' );
}

function themecheck_do_page() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }

// main global to hold our checks
global $themechecks;
$themechecks = array();

// interface that all checks should implement
interface themecheck
{
	// should return true for good/okay/acceptable, false for bad/not-okay/unacceptable
	public function check( $php_files, $css_files, $other_files );

	// should return an array of strings explaining any problems found
	public function getError();
}

// load all the checks in the checks directory
$dir = WP_PLUGIN_DIR . '/theme-check/checks';
if ( $handle = opendir( $dir ) ) {
	while ( ( $file = readdir( $handle ) ) !== false ) {
		if ( filetype( "$dir/".$file ) == 'file' && substr( $file,-4 ) == '.php' ) {
			include "$dir/".$file;
		}
	}
	closedir( $handle );
}
include( 'main.php' );
echo '<div id="theme-check" class="wrap">';
echo '<div id="icon-themes" class="icon32"><br /></div><h2>Theme-Check</h2>';


if (!isset($_POST['themename'])) tc_form();
if ( isset($_POST['themename'] )) check_main( $_POST['themename'] );
echo '</div>';
}