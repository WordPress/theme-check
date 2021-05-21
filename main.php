<?php
function check_main( $theme_slug ) {
	global $checkcount;

	$theme = wp_get_theme( $theme_slug );
	if ( ! $theme->exists() ) {
		return;
	}

	// Run the checks.
	$success = run_themechecks_against_theme( $theme, $theme_slug );

	// Display theme info.
	echo '<div class="theme-info">';
	echo '<div>';
	echo '<h2>' . esc_html__( 'Theme Info', 'theme-check' ) . ': </h2>';
	echo ( ! empty( $theme['Title'] ) ) ? '<p><b>' . esc_html__( 'Name:', 'theme-check' ) . '</b> ' . esc_html( $theme['Title'] ) . '</p>' : '';
	echo ( ! empty( $theme['Version'] ) ) ? '<p><b>' . esc_html__( 'Version:', 'theme-check' ) . '</b> ' . esc_html( $theme['Version'] ) . '</p>' : '';
	echo ( ! empty( $theme['AuthorName'] ) ) ? '<p><b>' . esc_html__( 'Author:', 'theme-check' ) . '</b> ' . esc_html( $theme['AuthorName'] ) . '</p>' : '';
	echo ( ! empty( $theme['AuthorURI'] ) ) ? '<p><b>' . esc_html__( 'Author URI:', 'theme-check' ) . '</b> <a href="' . esc_attr( $theme['AuthorURI'] ) . '">' . esc_html( $theme['AuthorURI'] ) . '</a></p>' : '';
	echo ( ! empty( $theme['URI'] ) ) ? '<p><b>' . esc_html__( 'Theme URI:', 'theme-check' ) . '</b> <a href="' . esc_attr( $theme['URI'] ) . '">' . esc_html( $theme['URI'] ) . '</a></p>' : '';
	echo ( ! empty( $theme['License'] ) ) ? '<p><b>' . esc_html__( 'License:', 'theme-check' ) . '</b> ' . esc_html( $theme['License'] ) . '</p>' : '';
	echo ( ! empty( $theme['License URI'] ) ) ? '<p><b>' . esc_html__( 'License URI:', 'theme-check' ) . '</b> ' . esc_html( $theme['License URI'] ) . '</p>' : '';
	echo ( ! empty( $theme['Tags'] ) ) ? '<p><b>' . esc_html__( 'Tags:', 'theme-check' ) . '</b> ' . esc_html( implode( ', ', $theme['Tags'] ) ) . '</p>' : '';
	echo ( ! empty( $theme['Description'] ) ) ? '<p><b>' . esc_html__( 'Description:', 'theme-check' ) . '</b> ' . esc_html( $theme['Description'] ) . '</p>' : '';

	if ( $theme->parent() ) {
		echo '<p>';
		printf(
			/* translators: %s: Name of the parent theme. */
			esc_html__( 'This is a child theme. The parent theme is: %s. These files have been included automatically!', 'theme-check' ),
			'<strong>' . esc_html( $theme['Template'] ) . '</strong>'
		);
		echo '</p>';
	}

	if ( $theme['Template Version'] /* Not supported by WordPress */ ) {
		$parent_theme = $theme->parent();
		if ( $theme['Template Version'] > $parent_theme['Version'] ) {
			echo '<p>';
			printf(
				esc_html__( 'This child theme requires at least version %1$s of theme %2$s to be installed. You only have %3$s please update the parent theme.', 'theme-check' ),
				'<strong>' . esc_html( $theme['Template Version'] ) . '</strong>',
				'<strong>' . esc_html( $parent_theme['Title'] ) . '</strong>',
				'<strong>' . esc_html( $parent_theme['Version'] ) . '</strong>'
			);
			echo '</p>';
		}

		if ( empty( $theme['Template Version'] ) ) {
			echo '<p>' . __( 'Child theme does not have the <strong>Template Version</strong> tag in style.css.', 'theme-check' ) . '</p>';
		} elseif ( $theme['Template Version'] < $parent_theme['Version'] ) {
			echo '<p>';
			printf(
				esc_html__( 'Child theme is only tested up to version %1$s of %2$s breakage may occur! %3$s installed version is %4$s', 'theme-check' ),
				esc_html( $theme['Template Version'] ),
				esc_html( $parent_theme['Title'] ),
				esc_html( $parent_theme['Title'] ),
				esc_html( $parent_theme['Version'] )
			);
			echo '</p>';
		}
	}

	echo '</div>';

	$screenshot = $theme->get_screenshot( 'relative' );
	if ( $screenshot ) {
		$screenshot_file = $theme->get_stylesheet_directory() . '/' . $screenshot;
		$image_size      = getimagesize( $screenshot_file );
		$image_filesize  = filesize( $screenshot_file );

		echo '<div><img style="max-height:180px;" src="' . esc_url( $theme->get_screenshot() ) . '" />';
		echo '<br /><div style="text-align:center">' . intval( $image_size[0] ) . 'x' . intval( $image_size[1] ) . ' ' . round( $image_filesize / 1024 ) . 'k</div></div>';
	}
	echo '</div><!-- .theme-info-->';

	$plugins = get_plugins( '/theme-check' );
	$version = explode( '.', $plugins['theme-check.php']['Version'] );
	echo '<div class="running-tests">' . sprintf(
		esc_html__( 'Running %1$s tests against %2$s using Guidelines Version: %3$s Plugin revision: %4$s', 'theme-check' ),
		'<strong>' . esc_html( $checkcount ) . '</strong>',
		'<strong>' . esc_html( $theme['Title'] ) . '</strong>',
		'<strong>' . esc_html( $version[0] ) . '</strong>',
		'<strong>' . esc_html( $version[1] ) . '</strong>'
	) . '</div>';

	$results = display_themechecks();


	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		echo '<div class="notice notice-warning"><p>';
		echo '<span class="tc-fail">';
		echo esc_html__( 'WARNING', 'theme-check' );
		echo '</span> ';
		echo '<strong>';
		echo esc_html__( 'WP_DEBUG is not enabled!', 'theme-check' );
		echo '</strong> ';
		printf(
			/* translators: %1$s is an opening anchor tag. %2$s is the closing part of the tag. */
			esc_html__( 'Please test your theme with %1$sdebug enabled%2$s before you upload!', 'theme-check' ),
			'<a href="https://wordpress.org/support/article/editing-wp-config-php/">',
			'</a>'
		);
		echo '</p></div>';
	}

	echo '<div class="tc-box">';

	if ( ! $success ) {
		echo '<h2>' . sprintf( __( 'One or more errors were found for %1$s.', 'theme-check' ), esc_html( $theme['Title'] ) ) . '</h2>';
	} else {
		echo '<h2>' . sprintf( __( '%1$s passed the tests', 'theme-check' ), esc_html( $theme['Title'] ) ) . '</h2>';
		tc_success();
	}

	echo '<ul class="tc-result">';
	echo wp_kses(
		$results,
		array(
			'li'     => array(),
			'span'   => array(
				'class' => array(),
			),
			'strong' => array(),
			'code' => array(),
			'pre' => array(),
			'a' => array(
				'href' => array(),
			),
		)
	);
	echo '</ul></div>';
}

function tc_intro() {
	?>
	<div class="tc-box">
	<h2><?php esc_html_e( 'About', 'theme-check' ); ?></h2>
	<p><?php esc_html_e( "The Theme Check plugin is an easy way to test your theme and make sure it's up to date with the latest theme review standards. With it, you can run all the same automated testing tools on your theme that WordPress.org uses for theme submissions.", 'theme-check' ); ?></p>
	<h3><?php esc_html_e( 'Contact', 'theme-check' ); ?></h3>
	<p><?php printf( __( 'If you have found a bug or would like to make a suggestion or contribution, please leave a post on the <a href="%1$s">WordPress plugin support forums</a>, ', 'theme-check' ), 'https://wordpress.org/support/plugin/theme-check/' ); ?></p>
	<p><?php printf( __( 'The code for Theme Check can be contributed to on <a href="%s">GitHub</a>.', 'theme-check' ), 'https://github.com/WordPress/theme-check' ); ?></p>
	<h3><?php esc_html_e( 'Testers', 'theme-check' ); ?></h3>
	<p><a href="https://make.wordpress.org/themes/"><?php esc_html_e( 'The WordPress Themes Team', 'theme-check' ); ?></a></p>
	</div>
	<?php
}

function tc_success() {
	?>
	<div class="tc-success">
	<?php esc_html_e( 'Now that your theme has passed the basic tests you need to check it properly using the test data before you upload it to the WordPress Themes Directory.', 'theme-check' ); ?>
	<?php
		printf(
			/* translators: %1$s is an opening anchor tag. %2$s is the closing part of the tag. */
			esc_html__( 'Make sure to review the %1$sguidelines%2$s before uploading a theme.', 'theme-check' ),
			'<a href="https://make.wordpress.org/themes/handbook/review/required/">',
			'</a>'
		);
		?>
	<h3><?php esc_html_e( 'Useful Links', 'theme-check' ); ?></h3>
	<ul>
	<li><a href="https://developer.wordpress.org/themes/"><?php esc_html_e( 'Theme Handbook', 'theme-check' ); ?></a></li>
	<li><a href="https://wordpress.org/support/forum/wp-advanced/"><?php esc_html_e( 'Developing with WordPress Forum', 'theme-check' ); ?></a></li>
	<li><a href="https://github.com/WPTRT/theme-unit-test"><?php esc_html_e( 'Theme Unit Tests', 'theme-check' ); ?></a></li>
	</ul></div>
	<?php
}

function tc_form() {
	echo '<form action="themes.php?page=themecheck" method="post">';
	echo '<select name="themename">';

	$selected_theme = isset( $_POST['themename'] ) ? wp_unslash( $_POST['themename'] ) : get_stylesheet();
	foreach ( wp_get_themes() as $theme ) {
		printf(
			'<option %s value="%s">%s</option>',
			selected( $selected_theme, $theme['Stylesheet'], false ),
			esc_attr( $theme['Stylesheet'] ),
			esc_html( $theme['Name'] )
		);
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

