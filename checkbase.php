<?php
// main global to hold our checks
global $themechecks;
$themechecks = array();

// counter for the checks
global $checkcount;
$checkcount = 0;

// interface that all checks should implement
interface themecheck
{
	// should return true for good/okay/acceptable, false for bad/not-okay/unacceptable
	public function check( $php_files, $css_files, $other_files );

	// should return an array of strings explaining any problems found
	public function getError();
}

// load all the checks in the checks directory
$dir = 'checks';
foreach (glob(dirname(__FILE__). "/{$dir}/*.php") as $file) {
	include $file;
}

function run_themechecks($php, $css, $other) {
	global $themechecks;
	$pass = true;
	foreach($themechecks as $check) {
		if ($check instanceof themecheck) {
			$pass = $pass & $check->check($php, $css, $other);
		}
	}
	return $pass;
}

function display_themechecks() {
	$results = '';
	global $themechecks;
	$errors = array();
	foreach ($themechecks as $check) {
		if ($check instanceof themecheck) {
			$error = $check->getError();
			$error = (array) $error;
			if (!empty($error)) {
				$errors = array_unique( array_merge( $error, $errors ) );
			}
		}
	}
	if (!empty($errors)) {
		rsort($errors);
		foreach ($errors as $e) {
		if ( defined( 'REVIEWER' ) ) {
			$results .= tc_trac( $e ) . "\r\n";
		} else {
			$results .= '<li>' . tc_trac( $e ) . '</li>';
			}
		}
	}
	if ( defined( 'REVIEWER' ) ) {

		if ( defined( 'TC_PRE' ) ) $results = TC_PRE . $results;
		$results = '<textarea cols=140 rows=20>' . strip_tags( $results );
		if ( defined( 'TC_POST' ) ) $results = $results . TC_POST;
		$results .= '</textarea>';
	}
	return $results;
}

function checkcount() {
	global $checkcount;
	$checkcount++;
}

// some functions theme checks use
function tc_grep( $error, $file ) {
	$lines = file( $file, FILE_IGNORE_NEW_LINES ); // Read the theme file into an array
	$line_index = 0;
	$bad_lines = '';
	foreach( $lines as $this_line )	{
		if ( stristr ( $this_line, $error ) ) {
			$error = str_replace( '"', "'", $error );
			$this_line = str_replace( '"', "'", $this_line );
			$error = ltrim( $error );
		$pre = ( FALSE !== ( $pos = strpos( $this_line, $error ) ) ? substr( $this_line, 0, $pos ) : FALSE );
		$pre = ltrim( htmlspecialchars( $pre ) );
			$bad_lines .= __("<pre class='tc-grep'>Line ", "themecheck") . ( $line_index+1 ) . ": " . $pre . htmlspecialchars( substr( stristr( $this_line, $error ), 0, 75 ) ) . "</pre>";
		}
		$line_index++;
	}
		return str_replace( $error, '<span class="tc-grep">' . $error . '</span>', $bad_lines );
}

function tc_preg( $preg, $file ) {
	$lines = file( $file, FILE_IGNORE_NEW_LINES ); // Read the theme file into an array
	$line_index = 0;
	$bad_lines = '';
	foreach( $lines as $this_line )
	{
		if ( preg_match( $preg, $this_line, $matches ) ) {
			$error = $matches[0];
			$this_line = str_replace( '"', "'", $this_line );
			$error = ltrim( $error );
		$pre = ( FALSE !== ( $pos = strpos( $this_line, $error ) ) ? substr( $this_line, 0, $pos ) : FALSE );
		$pre = ltrim( htmlspecialchars( $pre ) );
			$bad_lines .= __("<pre class='tc-grep'>Line ", "themecheck") . ( $line_index+1 ) . ": " . $pre . htmlspecialchars( substr( stristr( $this_line, $error ), 0, 75 ) ) . "</pre>";
		}
		$line_index++;

	}
		return str_replace( $error, '<span class="tc-grep">' . $error . '</span>', $bad_lines );
}

function tc_strxchr($haystack, $needle, $l_inclusive = 0, $r_inclusive = 0){
	if(strrpos($haystack, $needle)){
		//Everything before last $needle in $haystack.
		$left =  substr($haystack, 0, strrpos($haystack, $needle) + $l_inclusive);
		//Switch value of $r_inclusive from 0 to 1 and viceversa.
		$r_inclusive = ($r_inclusive == 0) ? 1 : 0;
		//Everything after last $needle in $haystack.
		$right =  substr(strrchr($haystack, $needle), $r_inclusive);
		//Return $left and $right into an array.
		return array($left, $right);
	} else {
		if(strrchr($haystack, $needle)) return array('', substr(strrchr($haystack, $needle), $r_inclusive));
		else return false;
	}
}

function tc_filename( $file ) {
		$filename = ( preg_match( '/themes\/[a-z0-9]*\/(.*)/', $file, $out ) ) ? $out[1] : '??' . basename( $file );
		return $filename;

}

function tc_trac( $e ) {
		$trac_left = array( '<strong>', '</strong>' );
		$trac_right= array( "'''", "'''" );
		$html_link = '/\<a href=\"(.*?)\"(.*?)\>(.*?)\<\/a\>/i';
		$html_new = '[$1 $3]';
		if ( defined( 'REVIEWER' ) ) {
			$e = preg_replace( $html_link, $html_new, $e);
			$e = str_replace($trac_left, $trac_right, $e);
			$e = preg_replace( '/<pre.*?>/', "\r\n{{{\r\n", $e);
			$e = str_replace( '</pre>', "\r\n}}}\r\n", $e);
		}
		return $e;
}

    function html_print_r( $data ) {
        $out = "\n<pre class='html-print-r'";
        $out .= " style='border: 1px solid #ccc; padding: 7px;'>\n";
        $out .= esc_html( print_r( $data, TRUE ) );
        $out .= "\n</pre>\n";

        echo $out;
    }
