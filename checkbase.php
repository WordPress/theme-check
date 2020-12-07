<?php
// main global to hold our checks.
global $themechecks;
$themechecks = array();

// counter for the checks.
global $checkcount;
$checkcount = 0;

// interface that all checks should implement.
interface themecheck {

	// should return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	public function check( $php_files, $css_files, $other_files );

	// should return an array of strings explaining any problems found.
	public function getError();
}

// load all the checks in the checks directory.
$dir = 'checks';
foreach ( glob( dirname( __FILE__ ) . "/{$dir}/*.php" ) as $file ) {
	include $file;
}

do_action( 'themecheck_checks_loaded' );

function run_themechecks( $php, $css, $other, $context = array() ) {
	global $themechecks;
	$pass = true;
	foreach ( $themechecks as $check ) {
		if ( $check instanceof themecheck ) {
			if ( $context && is_callable( array( $check, 'set_context' ) ) ) {
				$check->set_context( $context );
			}
			$pass = $pass & $check->check( $php, $css, $other );
		}
	}
	return $pass;
}

function display_themechecks() {
	$results = '';
	global $themechecks;
	$errors = array();
	foreach ( $themechecks as $check ) {
		if ( $check instanceof themecheck ) {
			$error = $check->getError();
			$error = (array) $error;
			if ( ! empty( $error ) ) {
				$errors = array_unique( array_merge( $error, $errors ) );
			}
		}
	}
	if ( ! empty( $errors ) ) {
		rsort( $errors );
		foreach ( $errors as $e ) {

			if ( defined( 'TC_TRAC' ) ) {
				$results .= ( isset( $_POST['s_info'] ) && preg_match( '/INFO/', $e ) ) ? '' : '* ' . tc_trac( $e ) . "\r\n";
			} else {
				$results .= ( isset( $_POST['s_info'] ) && preg_match( '/INFO/', $e ) ) ? '' : '<li>' . tc_trac( $e ) . '</li>';
			}
		}
	}

	if ( defined( 'TC_TRAC' ) ) {

		if ( defined( 'TC_PRE' ) ) {
			$results = TC_PRE . $results;
		}
		$results = '<textarea cols=140 rows=20>' . wp_strip_all_tags( $results );
		if ( defined( 'TC_POST' ) ) {
			$results = $results . TC_POST;
		}
		$results .= '</textarea>';
	}
	return $results;
}

function checkcount() {
	global $checkcount;
	$checkcount++;
}

// some functions theme checks use.
function tc_grep( $error, $file ) {
	if ( ! file_exists( $file ) ) {
		return '';
	}
	$lines      = file( $file, FILE_IGNORE_NEW_LINES ); // Read the theme file into an array.
	$line_index = 0;
	$bad_lines  = '';
	foreach ( $lines as $this_line ) {
		if ( stristr( $this_line, $error ) ) {
			$error      = str_replace( '"', "'", $error );
			$this_line  = str_replace( '"', "'", $this_line );
			$error      = ltrim( $error );
			$pos        = strpos( $this_line, $error );
			$pre        = ( false !== $pos ? substr( $this_line, 0, $pos ) : false );
			$pre        = ltrim( htmlspecialchars( $pre ) );
			$bad_lines .= "<pre class='tc-grep'>" . __( 'Line ', 'theme-check' ) . ( $line_index + 1 ) . ': ' . $pre . htmlspecialchars( substr( stristr( $this_line, $error ), 0, 75 ) ) . '</pre>';
		}
		$line_index++;
	}
	return str_replace( $error, '<span class="tc-grep">' . $error . '</span>', $bad_lines );
}

function tc_preg( $preg, $file ) {
	if ( ! file_exists( $file ) ) {
		return '';
	}
	$lines      = file( $file, FILE_IGNORE_NEW_LINES ); // Read the theme file into an array.
	$line_index = 0;
	$bad_lines  = '';
	$error      = '';
	foreach ( $lines as $this_line ) {
		if ( preg_match( $preg, $this_line, $matches ) ) {
			$error     = $matches[0];
			$this_line = str_replace( '"', "'", $this_line );
			$error     = ltrim( $error );
			$pre       = '';
			if ( ! empty( $error ) ) {
				$pos = strpos( $this_line, $error );
				$pre = ( false !== $pos ? substr( $this_line, 0, $pos ) : false );
			}
			$pre        = ltrim( htmlspecialchars( $pre ) );
			$bad_lines .= "<pre class='tc-grep'>" . __( 'Line ', 'theme-check' ) . ( $line_index + 1 ) . ': ' . $pre . htmlspecialchars( substr( stristr( $this_line, $error ), 0, 75 ) ) . '</pre>';
		}
		$line_index++;

	}
	return str_replace( $error, '<span class="tc-grep">' . $error . '</span>', $bad_lines );
}

function tc_filename( $file ) {
	$filename = ( preg_match( '/themes\/[a-z0-9-]*\/(.*)/', $file, $out ) ) ? $out[1] : basename( $file );
	return $filename;
}

function tc_trac( $e ) {
	$trac_left  = array( '<strong>', '</strong>' );
	$trac_right = array( "'''", "'''" );
	$html_link  = '/<a\s?href\s?=\s?[\'|"]([^"|\']*)[\'|"]>([^<]*)<\/a>/i';
	$html_new   = '[$1 $2]';
	if ( defined( 'TC_TRAC' ) ) {
		$e = preg_replace( $html_link, $html_new, $e );
		$e = str_replace( $trac_left, $trac_right, $e );
		$e = preg_replace( '/<pre.*?>/', "\r\n{{{\r\n", $e );
		$e = str_replace( '</pre>', "\r\n}}}\r\n", $e );
	}
	return $e;
}

