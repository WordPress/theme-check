<?php
function check_main() {
global $themechecks;
$files = listdir( TEMPLATEPATH );
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
			$failed = false;
			foreach( $themechecks as $check ) {
				if ( $check instanceof themecheck ) {
					if (! $check->check( $php, $css, $other ) ) {
						$failed = true;
					}
				}
			}

			// second loop, to display the errors
			$plugins = get_plugins();
			global $checkcount;
			$version = explode( '.', $plugins['theme-check/theme-check.php']['Version'] );
			echo 'Guidelines Version: <strong>'. $version[0] . '</strong> Plugin revision: <strong>'. $version[1] .'</strong><br />';
			echo $checkcount . ' checks ran against <strong> ' . get_option( 'template' ) . '</strong><br>';
			if ( !defined( 'WP_DEBUG' ) || WP_DEBUG == false ) echo '<span><strong>WP_DEBUG is not enabled!</strong> Please test your theme with <a href="http://codex.wordpress.org/Editing_wp-config.php">debug enabled</a> before you upload!</span>';

			// display the errors. Each checker class can return an array of strings as errors
			$dos2unix = array();
			$deprecated = array();
			$required = array();
			$cssneeded = array();
			$cssoptional = array();
			$critical = array();
			$short = array();
			$recommended = array();
			$info = array();
			foreach ( $themechecks as $check ) {
				if ( $check instanceof themecheck ) {
					$error = $check->getError();
					$error = (array) $error;
					if ( !empty( $error ) ) {
						foreach ( $error as $e ) {
									if ( preg_match( '/DEPRECATED/',$e ) ) { $e = str_replace( 'DEPRECATED','',$e ); array_push( $deprecated, $e ); }
									if ( preg_match( '/REQUIRED/',$e ) ) { $e = str_replace( 'REQUIRED','',$e ); array_push( $required, $e ); }
									if ( preg_match( '/CRITICAL/',$e ) ) { $e = str_replace( 'CRITICAL','',$e ); array_push( $critical, $e ); }
									if ( preg_match( '/RECOMMENDED/',$e ) ) { $e = str_replace( 'RECOMMENDED','',$e ); array_push( $recommended, $e ); }
									if ( preg_match( '/INFO/',$e ) ) { $e = str_replace( 'INFO','',$e ); array_push( $info, $e ); }
						}
					}
				}
			}
			if ( $deprecated || $required || $critical || $short ) {
				echo "<br /><h1>One or more errors were found.</h1>";
			} else {
				echo '<h2>' . get_option( 'template' ) . ' passed all the tests!</h2>';
				TC_success();
			}
			if ( $critical ) {
					echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
					echo '<ul>';
					foreach( $critical as $error ) {
					echo '<li><span style="color:red">Critical: </span>'.$error.'</li>';
					}
					echo '</ul>';
					echo '</div>';
					}

			if ( $deprecated ) {
					echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
					echo '<ul>';
					foreach( $deprecated as $error ) {
					echo '<li><span style="color:red">Deprecated: </span>'.$error.'</li>';
					}
					echo '</ul>';
					echo '</div>';
					}

			if ( $required ) {
					echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
					echo '<ul>';
					foreach( $required as $error ) {
					echo '<li><span style="color:red">Required: </span>'.$error.'</li>';
					}
					echo '</ul>';
					echo '</div>';
					}

			if ( $recommended ) {
					echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
					echo '<ul>';
					foreach( $recommended as $error ) {
					echo '<li><span style="color:blue">Recommended: </span>'.$error.'</li>';
					}
					echo '</ul>';
					echo '</div>';
					}

			if ( $info ) {
					echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
					echo '<ul>';
					foreach( $info as $error ) {
					echo '<li><span style="color:green">Info: </span>'.$error.'</li>';
					}
					echo '</ul>';
					echo '</div>';
					}
		}
	}


Function listdir( $start_dir='.' ) {

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
function checkcount() {
	global $checkcount;
	$checkcount++;
	}
	
function TC_success() {

echo 'Now your theme has passed the basic tests why not buy me a beer ;)<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="6GF2U8ZFUHLPA">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
';
}