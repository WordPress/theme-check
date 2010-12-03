<?php
function check_main( $theme ) {
global $themechecks;
$files = listdir( WP_CONTENT_DIR . '/themes/' . $theme );
		if ( $files ) {
				foreach( $files as $key => $filename ) {
				if ( substr( $filename, -4 ) == '.php' ) {
					$php[$filename] = php_strip_whitespace( $filename );
				}
				else if ( substr( $filename, -4 ) == '.css' ) {
					$css[$filename] = file_get_contents( $filename );
				}
				else {
					$other[$filename] = file_get_contents( $filename );
				}
			}

			// run the checks
			$failed = !run_themechecks($php, $css, $other);

			global $checkcount;
			tc_form();

			// second loop, to display the errors
			echo $checkcount . ' checks ran against <strong> ' . $theme . '</strong><br>';

			if ( $failed ) {
				echo "<br /><h1>One or more errors were found for " . $theme . ".</h1>";
			} else {
				echo '<h2>' . $theme . ' passed the tests</h2>';
				tc_success();
			}
			?>
			<style type="text/css">
			.tc-warning, .tc-required, .tc-fail {
				color:red;
			}

			.tc-recommended, .tc-pass {
				color: green;
			}

			.tc-info {
				color: blue;
			}

			.tc-grep span {
				color: red;
				font-weight: bold;
			}
			</style>
			<?php
			echo '<div style="padding:20px 0;border-top:1px solid #ccc;">';
			echo "<ul class='tc-result'>";
			display_themechecks();
			echo "</ul></div>";
		}
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

function tc_success() {
echo 'Now your theme has passed the basic tests why not buy me a beer ;)<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="6GF2U8ZFUHLPA">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
';
}

function tc_form() {
	$plugins = get_plugins( '/theme-check' );
	$version = explode( '.', $plugins['theme-check.php']['Version'] );
	echo 'Guidelines Version: <strong>'. $version[0] . '</strong> Plugin revision: <strong>'. $version[1] .'</strong><br />';

	if ( !defined( 'WP_DEBUG' ) || WP_DEBUG == false ) echo '<span><strong>WP_DEBUG is not enabled!</strong> Please test your theme with <a href="http://codex.wordpress.org/Editing_wp-config.php">debug enabled</a> before you upload!</span>';

	$themes = get_themes();
	echo '<form action="themes.php?page=themecheck" method="POST">';
	echo '<select name="themename">';
	foreach($themes as $name => $location) {
		echo '<option ';
		if ( basename(TEMPLATEPATH) === $location['Template'] ) echo 'selected ';
		echo 'value="' . $location['Template'] . '">' . $name . '</option>';
	}
	echo '</select>';
	echo '<input type="submit" value="Check it!" />';
	echo '</form>';
}