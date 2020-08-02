<?php
function check_main( $theme ) {
	global $themechecks, $data, $themename;
	$themename = $theme;
	$theme     = get_theme_root( $theme ) . "/$theme";
	$files     = listdir( $theme );
	$data      = tc_get_theme_data( $theme . '/style.css' );
	if ( $data['Template'] ) {
		// This is a child theme, so we need to pull files from the parent, which HAS to be installed.
		$parent = get_theme_root( $data['Template'] ) . '/' . $data['Template'];
		if ( ! tc_get_theme_data( $parent . '/style.css' ) ) { // This should never happen but we will check while were here!
			echo '<h2>';
			printf(
				/* translators: The parent theme name. */
				esc_html__( 'Parent theme %1$s not found! You have to have parent AND child-theme installed!', 'theme-check' ),
				'<strong>' . esc_html( $data['Template'] ) . '</strong>'
			);
			echo '</h2>';
			return;
		}
		$parent_data = tc_get_theme_data( $parent . '/style.css' );
		$themename   = basename( $parent );
		$files       = array_merge( listdir( $parent ), $files );
	}

	if ( $files ) {
		foreach ( $files as $key => $filename ) {
			if ( substr( $filename, -4 ) === '.php' && ! is_dir( $filename ) ) {
				$php[ $filename ] = file_get_contents( $filename );
				$php[ $filename ] = tc_strip_comments( $php[ $filename ] );
			} elseif ( substr( $filename, -4 ) === '.css' && ! is_dir( $filename ) ) {
				$css[ $filename ] = file_get_contents( $filename );
			} else {
				// In local development it might be useful to skip other files
				// (non .php or .css files) in dev directories.
				if ( apply_filters( 'tc_skip_development_directories', false ) ) {
					if ( tc_is_other_file_in_dev_directory( $filename ) ) {
						continue;
					}
				}
				$other[ $filename ] = ( ! is_dir( $filename ) ) ? file_get_contents( $filename ) : '';
			}
		}

		// Run the checks.
		$success = run_themechecks( $php, $css, $other );

		global $checkcount;

		// Second loop, to display the errors.
		echo '<h2>' . esc_html__( 'Theme Info', 'theme-check' ) . ': </h2>';
		echo '<div class="theme-info">';
		if ( file_exists( trailingslashit( WP_CONTENT_DIR . '/themes' ) . trailingslashit( basename( $theme ) ) . 'screenshot.png' ) ) {
			$image = getimagesize( $theme . '/screenshot.png' );
			echo '<div style="float:right" class="theme-info"><img style="max-height:180px;" src="' . trailingslashit( WP_CONTENT_URL . '/themes' ) . trailingslashit( basename( $theme ) ) . 'screenshot.png" />';
			echo '<br /><div style="text-align:center">' . $image[0] . 'x' . $image[1] . ' ' . round( filesize( $theme . '/screenshot.png' ) / 1024 ) . 'k</div></div>';
		}

		echo ( ! empty( $data['Title'] ) ) ? '<p><label>' . esc_html__( 'Title', 'theme-check' ) . '</label><span class="info">' . esc_html( $data['Title'] ) . '</span></p>' : '';
		echo ( ! empty( $data['Version'] ) ) ? '<p><label>' . esc_html__( 'Version', 'theme-check' ) . '</label><span class="info">' . esc_html( $data['Version'] ) . '</span></p>' : '';
		echo ( ! empty( $data['AuthorName'] ) ) ? '<p><label>' . esc_html__( 'Author', 'theme-check' ) . '</label><span class="info">' . esc_html( $data['AuthorName'] ) . '</span></p>' : '';
		echo ( ! empty( $data['AuthorURI'] ) ) ? '<p><label>' . esc_html__( 'Author URI', 'theme-check' ) . '</label><span class="info"><a href="' . esc_attr( $data['AuthorURI'] ) . '">' . $data['AuthorURI'] . '</a></span></p>' : '';
		echo ( ! empty( $data['URI'] ) ) ? '<p><label>' . esc_html__( 'Theme URI', 'theme-check' ) . '</label><span class="info"><a href="' . esc_attr( $data['URI'] ) . '">' . $data['URI'] . '</a></span></p>' : '';
		echo ( ! empty( $data['License'] ) ) ? '<p><label>' . esc_html__( 'License', 'theme-check' ) . '</label><span class="info">' . esc_html( $data['License'] ) . '</span></p>' : '';
		echo ( ! empty( $data['License URI'] ) ) ? '<p><label>' . esc_html__( 'License URI', 'theme-check' ) . '</label><span class="info">' . $data['License URI'] . '</span></p>' : '';
		echo ( ! empty( $data['Tags'] ) ) ? '<p><label>' . esc_html__( 'Tags', 'theme-check' ) . '</label><span class="info">' . implode( ', ', $data['Tags'] ) . '</span></p>' : '';
		echo ( ! empty( $data['Description'] ) ) ? '<p><label>' . esc_html__( 'Description', 'theme-check' ) . '</label><span class="info">' . $data['Description'] . '</span></p>' : '';

		if ( $data['Template'] ) {
			if ( $data['Template Version'] > $parent_data['Version'] ) {
				echo '<p>' . sprintf(
					esc_html__( 'This child theme requires at least version %1$s of theme %2$s to be installed. You only have %3$s please update the parent theme.', 'theme-check' ),
					'<strong>' . esc_html( $data['Template Version'] ) . '</strong>',
					'<strong>' . esc_html( $parent_data['Title'] ) . '</strong>',
					'<strong>' . esc_html( $parent_data['Version'] ) . '</strong>'
				) . '</p>';
			}
			echo '<p>' . sprintf(
				/* translators: %s: Name of the parent theme. */
				esc_html__( 'This is a child theme. The parent theme is: %s. These files have been included automatically!', 'theme-check' ),
				'<strong>' . esc_html( $data['Template'] ) . '</strong>'
			) . '</p>';
			if ( empty( $data['Template Version'] ) ) {
				echo '<p>' . esc_html__( 'Child theme does not have the <strong>Template Version</strong> tag in style.css.', 'theme-check' ) . '</p>';
			} else {
				echo ( $data['Template Version'] < $parent_data['Version'] ) ? '<p>' . sprintf( esc_html__( 'Child theme is only tested up to version %1$s of %2$s breakage may occur! %3$s installed version is %4$s', 'theme-check' ), esc_html( $data['Template Version'] ), esc_html( $parent_data['Title'] ), esc_html( $parent_data['Title'] ), esc_html( $parent_data['Version'] ) ) . '</p>' : '';
			}
		}
		echo '</div><!-- .theme-info-->';

		$plugins = get_plugins( '/theme-check' );
		$version = explode( '.', $plugins['theme-check.php']['Version'] );
		echo '<p>' . sprintf(
			esc_html__( ' Running %1$s tests against %2$s using Guidelines Version: %3$s Plugin revision: %4$s', 'theme-check' ),
			'<strong>' . esc_html( $checkcount ) . '</strong>',
			'<strong>' . esc_html( $data['Title'] ) . '</strong>',
			'<strong>' . esc_html( $version[0] ) . '</strong>',
			'<strong>' . esc_html( $version[1] ) . '</strong>'
		) . '</p>';
		$results = display_themechecks();
		if ( ! $success ) {
			echo '<h2>' . sprintf( esc_html__( 'One or more errors were found for %1$s.', 'theme-check' ), esc_html( $data['Title'] ) ) . '</h2>';
		} else {
			echo '<h2>' . sprintf( __( '%1$s passed the tests', 'theme-check' ), esc_html( $data['Title'] ) ) . '</h2>';
			tc_success();
		}
		if ( ! defined( 'WP_DEBUG' ) || WP_DEBUG === false ) {
			echo '<div class="updated">';
			echo '<span class="tc-fail">';
			echo esc_html__( 'WARNING', 'theme-check' );
			echo '</span> ';
			echo '<strong>';
			echo esc_html__( 'WP_DEBUG is not enabled!', 'theme-check' );
			echo '</strong>';
			printf(
				/* translators: %1$s is an opening anchor tag. %2$s is the closing part of the tag. */
				esc_html__( 'Please test your theme with %1$sdebug enabled%2$s before you upload!', 'theme-check' ),
				'<a href="https://wordpress.org/support/article/editing-wp-config-php/">',
				'</a>'
			);
			echo '</div>';
		}
		echo '<div class="tc-box">';
		echo '<ul class="tc-result">';
		echo wp_kses(
			$results,
			array(
				'li'     => array(),
				'span'   => array(
					'class' => array(),
				),
				'strong' => array(),
			)
		);
		echo '</ul></div>';
	}
}

// Strip comments from a PHP file in a way that will not change the underlying structure of the file.
function tc_strip_comments( $code ) {
	$strip    = array(
		T_COMMENT     => true,
		T_DOC_COMMENT => true,
	);
	$newlines = array(
		"\n" => true,
		"\r" => true,
	);
	$tokens   = token_get_all( $code );
	reset( $tokens );
	$return = '';
	$token  = current( $tokens );
	while ( $token ) {
		if ( ! is_array( $token ) ) {
			$return .= $token;
		} elseif ( ! isset( $strip[ $token[0] ] ) ) {
			$return .= $token[1];
		} else {
			for ( $i = 0, $token_length = strlen( $token[1] ); $i < $token_length; ++$i ) {
				if ( isset( $newlines[ $token[1][ $i ] ] ) ) {
					$return .= $token[1][ $i ];
				}
			}
		}
		$token = next( $tokens );
	}
	return $return;
}


function tc_intro() {
	?>
	<h2><?php esc_html_e( 'About', 'theme-check' ); ?></h2>
	<p><?php esc_html_e( "The Theme Check plugin is an easy way to test your theme and make sure it's up to date with the latest theme review standards. With it, you can run all the same automated testing tools on your theme that WordPress.org uses for theme submissions.", 'theme-check' ); ?></p>
	<h2><?php esc_html_e( 'Contact', 'theme-check' ); ?></h2>
	<p>
	<?php
	printf(
		esc_html__( 'Theme Check is maintained by %1$s and %2$s.', 'theme-check' ),
		'<a href="https://profiles.wordpress.org/otto42/">Otto42</a>',
		'<a href="https://profiles.wordpress.org/pross/">Pross</a>'
		); ?></p>
	<p><?php printf( __( 'If you have found a bug or would like to make a suggestion or contribution, please leave a post on the <a href="%1$s">WordPress forums</a>, or talk about it with the Themes Team on <a href="%2$s">Make WordPress Themes</a> site.', 'theme-check' ), 'https://wordpress.org/tags/theme-check?forum_id=10', 'https://make.wordpress.org/themes/'); ?></p>
	<p><?php printf( __( 'The code for Theme Check can be contributed to on <a href="%s">GitHub</a>.', 'theme-check' ), 'https://github.com/WordPress/theme-check' ); ?></p>
	<h3><?php esc_html_e( 'Testers', 'theme-check' ); ?></h3>
	<p><a href="https://make.wordpress.org/themes/"><?php esc_html_e( 'The WordPress Themes Team', 'theme-check' ); ?></a></p>
	<?php
}

function tc_success() {
	?>
	<div class="tc-success"><p><?php esc_html_e( 'Now that your theme has passed the basic tests you need to check it properly using the test data before you upload it to the WordPress Themes Directory.', 'theme-check' ); ?></p>
	<p>
		<?php
		printf(
			/* translators: %1$s is an opening anchor tag. %2$s is the closing part of the tag. */
			esc_html__( 'Make sure to review the guidelines at %1$sTheme Review%2$s before uploading a Theme.', 'theme-check' ),
			'<a href="https://make.wordpress.org/themes/handbook/review/required/">',
			'</a>'
		);
		?>
	</p>
	<h3><?php esc_html_e( 'Useful Links', 'theme-check' ); ?></h3>
	<ul>
	<li><a href="https://developer.wordpress.org/themes/"><?php esc_html_e( 'Theme Handbook', 'theme-check' ); ?></a></li>
	<li><a href="https://wordpress.org/support/forum/wp-advanced/"><?php esc_html_e( 'Developing with WordPress Forum', 'theme-check' ); ?></a></li>
	<li><a href="https://github.com/WPTRT/theme-unit-test"><?php esc_html_e( 'Theme Unit Tests', 'theme-check' ); ?></a></li>
	</ul></div>
	<?php
}

function tc_form() {
	$themes = tc_get_themes();
	echo '<form action="themes.php?page=themecheck" method="post">';
	echo '<select name="themename">';
	foreach ( $themes as $name => $location ) {
		echo '<option ';
		if ( isset( $_POST['themename'] ) ) {
			echo ( $location['Stylesheet'] === $_POST['themename'] ) ? 'selected="selected" ' : '';
		} else {
			echo ( basename( STYLESHEETPATH ) === $location['Stylesheet'] ) ? 'selected="selected" ' : '';
		}
		echo ( basename( STYLESHEETPATH ) === $location['Stylesheet'] ) ? 'value="' . $location['Stylesheet'] . '" style="font-weight:bold;">' . $name . '</option>' : 'value="' . $location['Stylesheet'] . '">' . $name . '</option>';
	}
	echo '</select>';
	echo '<input class="button" type="submit" value="' . esc_attr__( 'Check it!', 'theme-check' ) . '" />';
	if ( defined( 'TC_PRE' ) || defined( 'TC_POST' ) ) {
		echo ' <input name="trac" type="checkbox" /> ' . esc_html__( 'Output in Trac format.', 'theme-check' );
	}
	echo '<input name="s_info" type="checkbox" /> ' . esc_html__( 'Suppress INFO.', 'theme-check' );
	wp_nonce_field( 'themecheck-nonce' );
	echo '</form>';
}

/**
 * Used to allow some directories to be skipped during development.
 *
 * @param  string  $filename a filename/path
 * @return boolean
 */
function tc_is_other_file_in_dev_directory( $filename ) {
	$skip     = false;
	// Filterable List of dirs that you may want to skip other files in during
	// development.
	$dev_dirs = apply_filters(
		'tc_common_dev_directories',
		array(
			'node_modules',
			'vendor',
		)
	);
	foreach ( $dev_dirs as $dev_dir ) {
		if ( strpos( $filename, $dev_dir ) ) {
			$skip = true;
			break;
		}
	}
	return $skip;
}
