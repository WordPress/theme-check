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

		global $checkcount;

		// second loop, to display the errors
		echo '<h2>' . __( 'Theme Info', 'themecheck' ) . ': </h2>';
		echo '<div class="theme-info">';
		echo ( !empty( $data[ 'Title' ] ) ) ? '<p><label>' . __( 'Title', 'themecheck' ) . '</label><span class="info">' . $data[ 'Title' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'Version' ] ) ) ? '<p><label>' . __( 'Version', 'themecheck' ) . '</label><span class="info">' . $data[ 'Version' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'AuthorName' ] ) ) ? '<p><label>' . __( 'Author', 'themecheck' ) . '</label><span class="info">' . $data[ 'AuthorName' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'AuthorURI' ] ) ) ? '<p><label>' . __( 'Author URI', 'themecheck' ) . '</label><span class="info"><a href="' . $data[ 'AuthorURI' ] . '">' . $data[ 'AuthorURI' ] . '</a>' . '</span></p>' : '';
		echo ( !empty( $data[ 'URI' ] ) ) ? '<p><label>' . __( 'Theme URI', 'themecheck' ) . '</label><span class="info"><a href="' . $data[ 'URI' ] . '">' . $data[ 'URI' ] . '</a>' . '</span></p>' : '';
		echo ( !empty( $data[ 'License' ] ) ) ? '<p><label>' . __( 'License', 'themecheck' ) . '</label><span class="info">' . $data[ 'License' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'License URI' ] ) ) ? '<p><label>' . __( 'LicenseURI', 'themecheck' ) . '</label><span class="info">' . $data[ 'License URI' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'Tags' ] ) ) ? '<p><label>' . __( 'Tags', 'themecheck' ) . '</label><span class="info">' . implode( $data[ 'Tags' ], ', ') . '</span></p>' : '';
		echo ( !empty( $data[ 'Description' ] ) ) ? '<p><label>' . __( 'Description', 'themecheck' ) . '</label><span class="info">' . $data[ 'Description' ] . '</span></p>' : '';

		if ( $data[ 'Template' ] ) {
		if ( $data['Template Version'] > $parent_data['Version'] ) {
			echo "<p>This child theme requires at least version <strong>{$data['Template Version']}</strong> of theme <strong>{$parent_data['Title']}</strong> to be installed. You only have <strong>{$parent_data['Version']}</strong> please update the parent theme.</p>";
		}
			echo '<p>' . __( 'This is a child theme. The parent theme is', 'themecheck' ) . ': <strong>' . $data[ 'Template' ] . '</strong>. These files have been included automatically!</p>';
			if ( empty( $data['Template Version'] ) ) {
				echo '<p>Child theme does not have the <strong>Template Version</strong> tag in style.css.</p>';
			} else {
				echo ( $data['Template Version'] < $parent_data['Version'] ) ? "<p>Child theme is only tested up to version {$data['Template Version']} of {$parent_data['Title']} breakage may occur! {$parent_data['Title']} installed version is {$parent_data['Version']}</p>" : '';
			}
		 }
	    echo '</div><!-- .theme-info-->';
		$plugins = get_plugins( '/theme-check' );
		$version = explode( '.', $plugins['theme-check.php']['Version'] );
		echo '<p>Running <strong>' . $checkcount . '</strong> tests against <strong>' . $data[ 'Title' ] . '</strong> using Guidelines Version: <strong>'. $version[0] . '</strong> Plugin revision: <strong>'. $version[1] .'</strong></p>';
		$results = display_themechecks();
		$success = true;
		if (strpos( $results, 'WARNING') !== false) $success = false;
		if (strpos( $results, 'REQUIRED') !== false) $success = false;
		if ( $success === false ) {
			echo '<h2>' . __( 'One or more errors were found for ', 'themecheck' ) . $data[ 'Title' ] . '.</h2>';
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