<?php
/*
Plugin Name: Theme-Check
Plugin URI: http://pross.org.uk/plugins
Description: Run checks on the current theme before uploading to wordpress.
Author: Pross
Author URI: http://pross.org.uk
Version: 20101116.4
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

echo '<div class="wrap">';

check_main();

echo '</div>';
}
function tc_grep( $error, $file, $linenumber = true ) {
		$lines = file( $file, FILE_IGNORE_NEW_LINES ); // Read the theme file into an array
		$line_index = 0;
		$bad_lines = '';
		foreach( $lines as $this_line )
		{
			if ( stristr ( $this_line, $error ) ) 
			{
			$pre = ltrim( htmlspecialchars( stristr( $this_line, $error, true ) ) );
				$bad_lines .= "<pre>Line " . ( $line_index+1 ) . ": " . $pre. htmlspecialchars( substr( stristr( $this_line, $error ), 0, 75 ) ) . "</pre>";
			}
			$line_index++;
		}
	return $bad_lines;
}


function make_trac( $text ) {
	global $trac;
	if( !$trac ) {
		return $text;
	} else {
	
$trac_left = array( '<br />', '<strong>', '</strong>' );
$trac_right= array( "\r\n", "'''", "'''" );
$html_link = '/\<a href=\"(.*?)\"(.*?)\>(.*?)\<\/a\>/is';
$html_new = '[$1 $3]';
$code_left = array( '<pre>', '</pre>' );
$code_right = array( "\n{{{\n", "\n}}}\n" );

$text =   strip_tags( preg_replace( $html_link, $html_new, str_replace($trac_left, $trac_right, str_replace( $code_left, $code_right, $text ) ) ) );

return $text;
	
	}

}
