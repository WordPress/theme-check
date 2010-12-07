<?php
function check_main( $theme ) {
global $themechecks;
$files = listdir( WP_CONTENT_DIR . '/themes/' . $theme );
$data = get_theme_data( WP_CONTENT_DIR . '/themes/' . $theme . '/style.css');
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
			?>
			<style type="text/css">
			.tc-box {
			padding:10px 0;
			border-top:1px solid #ccc;
			border-bottom: 1px solid #ccc;
			}

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

			.tc-data {
				float: left;
				width: 80px;
			}
			</style>
			<?php
			global $checkcount;
			tc_form();

			// second loop, to display the errors

			echo '<strong>Theme Info:</strong>';
			echo '<br /><span class="tc-data">Title</span>:' . $data[ 'Title' ];
			echo '<br /><span class="tc-data">Version</span>:' . $data[ 'Version' ];
			echo '<br /><span class="tc-data">Author</span>:' . $data[ 'Author' ];
			echo '<br /><span class="tc-data">Description</span>:' . $data[ 'Description' ];
			if ( $data[ 'Template' ] ) echo '<br />This is a child theme. The parent theme is: ' . $data[ 'Template' ];

			$plugins = get_plugins( '/theme-check' );
			$version = explode( '.', $plugins['theme-check.php']['Version'] );
			echo '<br />Running <strong>' . $checkcount . '</strong> tests against <strong>' . $data[ 'Title' ] . '</strong> using Guidelines Version: <strong>'. $version[0] . '</strong> Plugin revision: <strong>'. $version[1] .'</strong><br />';

			if ( $failed ) {
				echo '<h3>One or more errors were found for ' . $data[ 'Title' ] . '.</h3>';
			} else {
				echo '<h2>' . $data[ 'Title' ] . ' passed the tests</h2>';
				tc_success();
			}
			if ( !defined( 'WP_DEBUG' ) || WP_DEBUG == false ) echo '<div class="updated"><span class="tc-fail">WARNING</span> <strong>WP_DEBUG is not enabled!</strong> Please test your theme with <a href="http://codex.wordpress.org/Editing_wp-config.php">debug enabled</a> before you upload!</div>';

			echo '<div class="tc-box">';
			echo '<ul class="tc-result">';
			display_themechecks();
			echo '</ul></div>';
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