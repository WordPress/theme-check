<?php
function check_main( $theme ) {
	global $themechecks, $data, $themename;
	$themename = $theme;
	$theme = get_theme_root( $theme ) . "/$theme";
	$files = listdir( $theme );
	$data = get_theme_data( $theme . '/style.css' );
	if ( $data[ 'Template' ] ) {
		// This is a child theme, so we need to pull files from the parent, which HAS to be installed.
		$parent = get_theme_root( $data[ 'Template' ] ) . '/' . $data['Template'];
		if ( !get_theme_data( $parent . '/style.css' ) ) { // This should never happen but we will check while were here!
			echo '<h2>Parent theme <strong>' . $data[ 'Template' ] . ' not found! You have to have parent AND child-theme installed!';
			return;
		}
		$parent_data = get_theme_data( $parent . '/style.css' );
		$themename = basename( $parent );
		$files = array_merge( listdir( $parent ), $files );
	}

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
			background: yellow;
		}

		.tc-data {
			float: left;
			width: 85px;
			clear: both;
		}

		.tc-header {
			width: 1024px;
			float: left;
			padding-left: 5px;
		}
		.tc-success {
		}
		</style>
		<?php
		global $checkcount;

		// second loop, to display the errors
		echo '<strong>' . __( 'Theme Info', 'themecheck' ) . ': </strong>';
		echo '<br /><div class="tc-data">' . __( 'Title', 'themecheck' ) . '</div><div class="tc-header">' . $data[ 'Title' ] . '</div>';
		echo '<br /><div class="tc-data">' . __( 'Theme slug', 'themecheck' ) . '</div><div class="tc-header">' . $themename . '</div>';
		echo '<br /><div class="tc-data">' . __( 'Version', 'themecheck' ) . '</div><div class="tc-header">' . $data[ 'Version' ] . '</div>';
		echo '<br /><div class="tc-data">' . __( 'Author', 'themecheck' ) . '</div><div class="tc-header">' . $data[ 'AuthorName' ] . '</div>';
		echo '<br /><div class="tc-data">' . __( 'Author URI', 'themecheck' ) . '</div><div class="tc-header"><a href="' . $data[ 'AuthorURI' ] . '">' . $data[ 'AuthorURI' ] . '</a>' . '</div>';
		echo '<br /><div class="tc-data">' . __( 'Theme URI', 'themecheck' ) . '</div><div class="tc-header"><a href="' . $data[ 'URI' ] . '">' . $data[ 'URI' ] . '</a>' . '</div>';
		echo '<br /><div class="tc-data">' . __( 'License', 'themecheck' ) . '</div><div class="tc-header">' . $data[ 'License' ] . '</div>';
		echo '<br /><div class="tc-data">' . __( 'LicenseURI', 'themecheck' ) . '</div><div class="tc-header">' . $data[ 'License URI' ] . '</div>';

		echo '<br /><div class="tc-data">' . __( 'Tags', 'themecheck' ) . '</div><div class="tc-header">' . implode( $data[ 'Tags' ], ', ') . '</div>';
		echo '<br /><div class="tc-data">' . __( 'Description', 'themecheck' ) . '</div><div class="tc-header">' . $data[ 'Description' ] . '</div>';
		echo '<br style="clear:both" />';
		if ( $data[ 'Template' ] ) {
		if ( $data['Template Version'] > $parent_data['Version'] ) {
			echo "<br />This child theme requires at least version <strong>{$data['Template Version']}</strong> of theme <strong>{$parent_data['Title']}</strong> to be installed. You only have <strong>{$parent_data['Version']}</strong> please update the parent theme.";
		}
			echo '<br />' . __( 'This is a child theme. The parent theme is', 'themecheck' ) . ': <strong>' . $data[ 'Template' ] . '</strong>. These files have been included automatically!';
			if ( empty( $data['Template Version'] ) ) {
				echo '<br />Child theme does not have the <strong>Template Version</strong> tag in style.css.';
			} else {
				echo ( $data['Template Version'] < $parent_data['Version'] ) ? "<br />Child theme is only tested up to version {$data['Template Version']} of {$parent_data['Title']} breakage may occur! {$parent_data['Title']} installed version is {$parent_data['Version']}" : '';
			}
		 }
		$plugins = get_plugins( '/theme-check' );
		$version = explode( '.', $plugins['theme-check.php']['Version'] );
		echo '<br /><br />Running <strong>' . $checkcount . '</strong> tests against <strong>' . $data[ 'Title' ] . '</strong> using Guidelines Version: <strong>'. $version[0] . '</strong> Plugin revision: <strong>'. $version[1] .'</strong><br />';

		$results = display_themechecks();
		$success = true;
		if (strpos( $results, 'WARNING') !== false) $success = false;
		if (strpos( $results, 'REQUIRED') !== false) $success = false;
		if ( $success === false ) {
			echo '<h3>' . __( 'One or more errors were found for ', 'themecheck' ) . $data[ 'Title' ] . '.</h3>';
		} else {
			echo '<h2>' . $data[ 'Title' ] . __( ' passed the tests', 'themecheck' ) . '</h2>';
			tc_success();
		}
		if ( !defined( 'WP_DEBUG' ) || WP_DEBUG == false ) echo '<div class="updated"><span class="tc-fail">WARNING</span> ' . __( '<strong>WP_DEBUG is not enabled!</strong> Please test your theme with <a href="http://codex.wordpress.org/Editing_wp-config.php">debug enabled</a> before you upload!</div>', 'themecheck' );

		echo '<div class="tc-box">';
		echo '<ul class="tc-result">';
		echo $results;
		echo '</ul></div>';
	}
}