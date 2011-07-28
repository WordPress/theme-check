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
		if (file_exists( trailingslashit( WP_CONTENT_DIR . '/themes' ) . trailingslashit( basename( $theme ) ) . 'screenshot.png' ) ) {
			$image = getimagesize( $theme . '/screenshot.png' );
		echo '<div style="float:right" class="theme-info"><img style="max-height:180px;" src="' . trailingslashit( WP_CONTENT_URL . '/themes' ) . trailingslashit( basename( $theme ) ) . 'screenshot.png" />';
		
		echo '<br /><div style="text-align:center">' . $image[0] . 'x' . $image[1] . ' ' . round( filesize( $theme . '/screenshot.png' )/1024 ) . 'k</div></div>';
		


		}
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


function tc_intro() {
	_e( '<h2>About</h2>', 'themecheck' );
	_e( '<p>The theme check plugin is an easy way to test your theme and make sure it\'s up to spec with the latest theme review standards.<br />', 'themecheck' );
	_e( 'With it, you can run all the same automated testing tools on your theme that WordPress.org uses for theme submissions.</p>', 'themecheck' );
	_e( '<h2>Contact</h2>', 'themecheck' );
	_e( '<p>Theme-Check is maintained by <a href="http://profiles.wordpress.org/users/pross/">Pross</a> and <a href="http://profiles.wordpress.org/users/otto42/">Otto42</a><br />', 'themecheck' );
	_e( 'If you have found a bug or would like to make a suggestion or contribution why not join the <a href="http://wordpress.org/extend/themes/contact/">theme-reviewers mailing list</a><br />', 'themecheck' );
	_e( 'or leave a post on the <a href="http://wordpress.org/tags/theme-check?forum_id=10">WordPress forums</a>.<br /></p>', 'themecheck' );
	echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick" /><input type="hidden" name="hosted_button_id" value="2V7F4QYMWMBL6" /><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" /><img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /></form>';
	_e( '<h2>Contributors</h2>', 'themecheck' );
	_e( '<h3>localization</h3>', 'themecheck' );
	echo '<ul>';
	echo '<li><a href="http://www.onedesigns.com/">Daniel Tara</a></li>';
	echo '<li><a href="http://themeid.com/">Emil Uzelac</a></li>';
	echo '</ul>';
	_e( '<h3>Testers</h3>', 'themecheck' );
	_e( '<p><a href="http://make.wordpress.org/themes/">The WordPress Theme Review Team</a></p>', 'themecheck' ); 
}

function tc_success() {
	_e( '<div class="tc-success"><p>Now your theme has passed the basic tests you need to check it properly using the test data before you upload to the WordPress Themes Directory.</p>', 'themecheck' );
	echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick" /><input type="hidden" name="hosted_button_id" value="2V7F4QYMWMBL6" /><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" /><img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /></form>';
	_e( '<p>Make sure to review the guidelines at <a href="http://codex.wordpress.org/Theme_Review">Theme Review</a> before uploading a Theme.</p>', 'themecheck' );
	_e( '<h3>Codex Links</h3>', 'themecheck' );
	echo '<ul>';
	_e( '<li><a href="http://codex.wordpress.org/Theme_Development">Theme Development</a></li>', 'themecheck' );
	_e( '<li><a href="http://wordpress.org/support/forum/5">Themes and Templates forum</a></li>', 'themecheck' );
	_e( '<li><a href="http://codex.wordpress.org/Theme_Unit_Test">Theme Unit Tests</a></li>', 'themecheck' );
	echo '</ul></div>';
}

function tc_form() {
	$themes = get_themes();
	echo '<form action="themes.php?page=themecheck" method="post">';
	echo '<select name="themename">';
	foreach( $themes as $name => $location ) {
		echo '<option ';
		if ( isset( $_POST['themename'] ) ) {
			echo ( $location['Stylesheet'] === $_POST['themename'] ) ? 'selected="selected" ' : ''; 
		} else {
			echo ( basename( STYLESHEETPATH ) === $location['Stylesheet'] ) ? 'selected="selected" ' : '';
		}
		echo ( basename( STYLESHEETPATH ) === $location['Stylesheet'] ) ? 'value="' . $location['Stylesheet'] . '" style="font-weight:bold;">' . $name . '</option>' : 'value="' . $location['Stylesheet'] . '">' . $name . '</option>';
	}
	echo '</select>';
	echo '<input class="button" type="submit" value="' . __( 'Check it!', 'themecheck' ) . '" />';
	if ( defined( 'TC_PRE' ) || defined( 'TC_POST' ) ) echo ' <input name="trac" type="checkbox" /> ' . __( 'Output in Trac format.', 'themecheck' );
	echo ' <input name="s_info" type="checkbox" /> ' . __( 'Suppress INFO.', 'themecheck' );
	echo '</form>';
}