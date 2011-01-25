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
		$filename = ( preg_match( '/themes\/[a-z0-9]*\/(.*)/', $file, $out ) ) ? $out[1] : basename( $file );
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

function listdir( $start_dir='.' ) {
	$files = array();
	if ( is_dir( $start_dir ) ) {
		$fh = opendir( $start_dir );
		while ( ( $file = readdir( $fh ) ) !== false ) {
			# loop through the files, skipping . and .., and recursing if necessary
			if ( strcmp( $file, '.' )==0 || strcmp( $file, '..' )==0 ) continue;
			$filepath = $start_dir . '/' . $file;
			if ( is_dir( $filepath ) )
				$files = array_merge( $files, listdir( $filepath ) );
			else
				array_push( $files, $filepath );
		}
	closedir( $fh );
	} else {
		# false if the function was called with an invalid non-directory argument
		$files = false;
		}
	return $files;
}

function html_print_r( $data ) {
        $out = "\n<pre class='html-print-r'";
        $out .= " style='border: 1px solid #ccc; padding: 7px;'>\n";
        $out .= esc_html( print_r( $data, TRUE ) );
        $out .= "\n</pre>\n";

        echo $out;
    }

function tc_add_headers( $extra_headers ) {
	$extra_headers = array( 'License', 'License URI' );
	return $extra_headers;
}

function tc_intro() {
echo '<h3>Contact</h3>
<p>Theme-Check is maintained by <a href="http://profiles.wordpress.org/users/pross/">Pross</a> and <a href="http://profiles.wordpress.org/users/otto42/">Otto42</a><br />
If you have found a bug or would like to make a suggestion or contribution why not join the <a href="http://wordpress.org/extend/themes/contact/">theme-reviewers mailing list</a><br />
or leave a post on the <a href="http://wordpress.org/tags/theme-check?forum_id=10">WordPress forums</a>.<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="2V7F4QYMWMBL6">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>';
}

function tc_success() {
echo '<div class="tc-success">Now your theme has passed the basic tests you need to check it properly using the test data before you upload to the WordPress Themes Directory.<br />
<br />
Make sure to review the guidelines at <a href="http://codex.wordpress.org/Theme_Review">Theme Review</a> before uploading a Theme.
<h3>Codex Links</h3>
<p>
<a href="http://codex.wordpress.org/Theme_Development">Theme Development</a><br />
<a href="http://wordpress.org/support/forum/5">Themes and Templates forum</a><br />
<a href="http://codex.wordpress.org/Theme_Unit_Test">Theme Unit Tests</a>
</p>
</div>
';
}

function tc_form() {
	$themes = get_themes();
	echo '<form action="themes.php?page=themecheck" method="POST">';
	echo '<select name="themename">';
	foreach($themes as $name => $location) {
		echo '<option ';
		if ( basename(TEMPLATEPATH) === $location['Stylesheet'] ) echo 'selected ';
		echo 'value="' . $location['Stylesheet'] . '">' . $name . '</option>';
	}
	echo '</select>';
	echo '<input type="submit" value="' . __( 'Check it!', 'themecheck' ) . '" />';
	echo '</form>';
}