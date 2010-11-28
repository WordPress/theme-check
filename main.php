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
			$failed = false;
			foreach( $themechecks as $check ) {
				if ( $check instanceof themecheck ) {
					if (! $check->check( $php, $css, $other ) ) {
						$failed = true;
					}
				}
			}
			global $checkcount;
			tc_form();
			// second loop, to display the errors
			echo $checkcount . ' checks ran against <strong> ' . $theme . '</strong><br>';
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
									if ( preg_match( '/DEPRECATED/',$e ) ) { $e = str_replace( 'DEPRECATED','',$e ); array_push( $deprecated, make_trac( $e ) ); }
									if ( preg_match( '/REQUIRED/',$e ) ) { $e = str_replace( 'REQUIRED','',$e ); array_push( $required, make_trac( $e ) ); }
									if ( preg_match( '/CRITICAL/',$e ) ) { $e = str_replace( 'CRITICAL','',$e ); array_push( $critical, make_trac( $e ) ); }
									if ( preg_match( '/RECOMMENDED/',$e ) ) { $e = str_replace( 'RECOMMENDED','',$e ); array_push( $recommended, make_trac( $e ) ); }
									if ( preg_match( '/INFO/',$e ) ) { $e = str_replace( 'INFO','',$e ); array_push( $info, make_trac( $e ) ); }
						}
					}
				}
			}
			if ( $deprecated || $required || $critical || $short ) {
				echo "<br /><h1>One or more errors were found for " . $theme . ".</h1>";
			} else {
				echo '<h2>' . $theme . ' passed all the tests!</h2>';
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
function tc_grep( $error, $file ) {
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