<?php
// main global to hold our checks
global $themechecks;
$themechecks = array();

// counter for the checks
global $checkcount;
$checkcount = 0;

// interface that all checks should implement.
interface themecheck {

	// should return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	public function check( $php_files, $css_files, $other_files );

	// should return an array of strings explaining any problems found.
	public function getError();
}

// load all the checks in the checks directory.
$dir = 'checks';
foreach ( glob( dirname( __FILE__ ) . "/{$dir}/*.php" ) as $file ) {
	include $file;
}

do_action( 'themecheck_checks_loaded' );

function run_themechecks( $php, $css, $other ) {
	/*
	echo '<pre>';
	foreach( $php as $key => $value ) {
		var_dump( $key );
	}

	foreach( $other as $key => $value ) {
		var_dump( $key );
	}
	echo '</pre>';
	*/


	global $themechecks;
	$pass = true;

	tc_adapt_checks_for_fse_themes( $php, $css, $other );

	foreach ( $themechecks as $check ) {
		if ( $check instanceof themecheck ) {
			$pass = $pass & $check->check( $php, $css, $other );
		}
	}
	return $pass;
}

function display_themechecks() {
	$results = '';
	global $themechecks;
	$errors = array();
	foreach ( $themechecks as $check ) {
		if ( $check instanceof themecheck ) {
			$error = $check->getError();
			$error = (array) $error;
			if ( ! empty( $error ) ) {
				$errors = array_unique( array_merge( $error, $errors ) );
			}
		}
	}
	if ( ! empty( $errors ) ) {
		rsort( $errors );
		foreach ( $errors as $e ) {

			if ( defined( 'TC_TRAC' ) ) {
				$results .= ( isset( $_POST['s_info'] ) && preg_match( '/INFO/', $e ) ) ? '' : '* ' . tc_trac( $e ) . "\r\n";
			} else {
				$results .= ( isset( $_POST['s_info'] ) && preg_match( '/INFO/', $e ) ) ? '' : '<li>' . tc_trac( $e ) . '</li>';
			}
		}
	}

	if ( defined( 'TC_TRAC' ) ) {

		if ( defined( 'TC_PRE' ) ) {
			$results = TC_PRE . $results;
		}
		$results = '<textarea cols=140 rows=20>' . wp_strip_all_tags( $results );
		if ( defined( 'TC_POST' ) ) {
			$results = $results . TC_POST;
		}
		$results .= '</textarea>';
	}
	return $results;
}

function checkcount() {
	global $checkcount;
	$checkcount++;
}

// some functions theme checks use.
function tc_grep( $error, $file ) {
	if ( ! file_exists( $file ) ) {
		return '';
	}
	$lines      = file( $file, FILE_IGNORE_NEW_LINES ); // Read the theme file into an array.
	$line_index = 0;
	$bad_lines  = '';
	foreach ( $lines as $this_line ) {
		if ( stristr( $this_line, $error ) ) {
			$error      = str_replace( '"', "'", $error );
			$this_line  = str_replace( '"', "'", $this_line );
			$error      = ltrim( $error );
			$pos = strpos( $this_line, $error );
			$pre        = ( false !== $pos ? substr( $this_line, 0, $pos ) : false );
			$pre        = ltrim( htmlspecialchars( $pre ) );
			$bad_lines .= "<pre class='tc-grep'>" . __( 'Line ', 'theme-check' ) . ( $line_index + 1 ) . ': ' . $pre . htmlspecialchars( substr( stristr( $this_line, $error ), 0, 75 ) ) . '</pre>';
		}
		$line_index++;
	}
	return str_replace( $error, '<span class="tc-grep">' . $error . '</span>', $bad_lines );
}

function tc_preg( $preg, $file ) {
	if ( ! file_exists( $file ) ) {
		return '';
	}
	$lines      = file( $file, FILE_IGNORE_NEW_LINES ); // Read the theme file into an array.
	$line_index = 0;
	$bad_lines  = '';
	$error      = '';
	foreach ( $lines as $this_line ) {
		if ( preg_match( $preg, $this_line, $matches ) ) {
			$error     = $matches[0];
			$this_line = str_replace( '"', "'", $this_line );
			$error     = ltrim( $error );
			$pre       = '';
			if ( ! empty( $error ) ) {
				$pos = strpos( $this_line, $error );
				$pre = ( false !== $pos ? substr( $this_line, 0, $pos ) : false );
			}
			$pre        = ltrim( htmlspecialchars( $pre ) );
			$bad_lines .= "<pre class='tc-grep'>" . __( 'Line ', 'theme-check' ) . ( $line_index + 1 ) . ': ' . $pre . htmlspecialchars( substr( stristr( $this_line, $error ), 0, 75 ) ) . '</pre>';
		}
		$line_index++;

	}
	return str_replace( $error, '<span class="tc-grep">' . $error . '</span>', $bad_lines );
}

function tc_filename( $file ) {
	$filename = ( preg_match( '/themes\/[a-z0-9-]*\/(.*)/', $file, $out ) ) ? $out[1] : basename( $file );
	return $filename;
}

function tc_trac( $e ) {
	$trac_left  = array( '<strong>', '</strong>' );
	$trac_right = array( "'''", "'''" );
	$html_link  = '/<a\s?href\s?=\s?[\'|"]([^"|\']*)[\'|"]>([^<]*)<\/a>/i';
	$html_new   = '[$1 $2]';
	if ( defined( 'TC_TRAC' ) ) {
		$e = preg_replace( $html_link, $html_new, $e );
		$e = str_replace( $trac_left, $trac_right, $e );
		$e = preg_replace( '/<pre.*?>/', "\r\n{{{\r\n", $e );
		$e = str_replace( '</pre>', "\r\n}}}\r\n", $e );
	}
	return $e;
}

function listdir( $dir ) {
	$files        = array();
	$dir_iterator = new RecursiveDirectoryIterator( $dir );
	$iterator     = new RecursiveIteratorIterator( $dir_iterator, RecursiveIteratorIterator::SELF_FIRST );

	foreach ( $iterator as $file ) {
		array_push( $files, $file->getPathname() );
	}
	return $files;
}

function get_theme_data_from_contents( $theme_data ) {
	$themes_allowed_tags = array(
		'a'       => array(
			'href'  => array(),
			'title' => array(),
		),
		'abbr'    => array(
			'title' => array(),
		),
		'acronym' => array(
			'title' => array(),
		),
		'code'    => array(),
		'em'      => array(),
		'strong'  => array(),
	);

	$theme_data = str_replace( '\r', '\n', $theme_data );
	preg_match( '|^[ \t\/*#@]*Theme Name:(.*)$|mi', $theme_data, $theme_name );
	preg_match( '|^[ \t\/*#@]*Theme URI:(.*)$|mi', $theme_data, $theme_uri );
	preg_match( '|^[ \t\/*#@]*Description:(.*)$|mi', $theme_data, $description );

	if ( preg_match( '|^[ \t\/*#@]*Author URI:(.*)$|mi', $theme_data, $author_uri ) ) {
		$author_uri = esc_url( trim( $author_uri[1] ) );
	} else {
		$author_uri = '';
	}

	if ( preg_match( '|^[ \t\/*#@]*Template:(.*)$|mi', $theme_data, $template ) ) {
		$template = wp_kses( trim( $template[1] ), $themes_allowed_tags );
	} else {
		$template = '';
	}

	if ( preg_match( '|^[ \t\/*#@]*Version:(.*)|mi', $theme_data, $version ) ) {
		$version = wp_kses( trim( $version[1] ), $themes_allowed_tags );
	} else {
		$version = '';
	}

	if ( preg_match( '|^[ \t\/*#@]*Status:(.*)|mi', $theme_data, $status ) ) {
		$status = wp_kses( trim( $status[1] ), $themes_allowed_tags );
	} else {
		$status = 'publish';
	}

	if ( preg_match( '|^[ \t\/*#@]*Tags:(.*)|mi', $theme_data, $tags ) ) {
		$tags = array_map( 'trim', explode( ',', wp_kses( trim( $tags[1] ), array() ) ) );
	} else {
		$tags = array();
	}

	$theme = ( isset( $theme_name[1] ) ) ? wp_kses( trim( $theme_name[1] ), $themes_allowed_tags ) : '';

	$theme_uri = ( isset( $theme_uri[1] ) ) ? esc_url( trim( $theme_uri[1] ) ) : '';

	$description = ( isset( $description[1] ) ) ? wp_kses( trim( $description[1] ), $themes_allowed_tags ) : '';

	if ( preg_match( '|^[ \t\/*#@]*Author:(.*)$|mi', $theme_data, $author_name ) ) {
		if ( empty( $author_uri ) ) {
			$author = wp_kses( trim( $author_name[1] ), $themes_allowed_tags );
		} else {
			$author = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $author_uri, __( 'Visit author homepage', 'theme-check' ), wp_kses( trim( $author_name[1] ), $themes_allowed_tags ) );
		}
	} else {
		$author = __( 'Anonymous', 'theme-check' );
	}

	return array(
		'Name'        => $theme,
		'Title'       => $theme,
		'URI'         => $theme_uri,
		'Description' => $description,
		'Author'      => $author,
		'Author_URI'  => $author_uri,
		'Version'     => $version,
		'Template'    => $template,
		'Status'      => $status,
		'Tags'        => $tags,
	);
}

/*
 * 3.3/3.4 compat
 *
 */
function tc_get_themes() {

	if ( ! class_exists( 'WP_Theme' ) ) {
		return wp_get_theme();
	}

	global $wp_themes;
	if ( isset( $wp_themes ) ) {
		return $wp_themes;
	}

	$themes    = wp_get_themes();
	$wp_themes = array();

	foreach ( $themes as $theme ) {
		$name = $theme->get( 'Name' );
		if ( isset( $wp_themes[ $name ] ) ) {
			$wp_themes[ $name . '/' . $theme->get_stylesheet() ] = $theme;
		} else {
			$wp_themes[ $name ] = $theme;
		}
	}

	return $wp_themes;
}

function tc_get_theme_data( $theme_file ) {

	if ( ! class_exists( 'WP_Theme' ) ) {
		return wp_get_theme( $theme_file );
	}

	$theme = new WP_Theme( basename( dirname( $theme_file ) ), dirname( dirname( $theme_file ) ) );

	$theme_data = array(
		'Name'             => $theme->get( 'Name' ),
		'URI'              => $theme->display( 'ThemeURI', true, false ),
		'Description'      => $theme->display( 'Description', true, false ),
		'Author'           => $theme->display( 'Author', true, false ),
		'AuthorURI'        => $theme->display( 'AuthorURI', true, false ),
		'Version'          => $theme->get( 'Version' ),
		'Template'         => $theme->get( 'Template' ),
		'Status'           => $theme->get( 'Status' ),
		'Tags'             => $theme->get( 'Tags' ),
		'Title'            => $theme->get( 'Name' ),
		'AuthorName'       => $theme->display( 'Author', false, false ),
		'License'          => $theme->display( 'License', false, false ),
		'License URI'      => $theme->display( 'License URI', false, false ),
		'Template Version' => $theme->display( 'Template Version', false, false ),
	);
	return $theme_data;
}

/**
 * Adapt the Theme Checks if the theme is an experiment Full-Site Editing theme.
 *
 * @param array $php_files   The theme's PHP files.
 * @param array $css_files   The theme's CSS files.
 * @param array $other_files Any other theme files.
 *
 * @return bool Whether the theme checks were adapted for FSE or not.
 */
function tc_adapt_checks_for_fse_themes( $php_files, $css_files, $other_files ) {
	global $themechecks;

	// Get a list of all non PHP and CSS file paths, relative to the theme root.
	$other_filenames = array();
	foreach ( $other_files as $path => $contents ) {
		$other_filenames[] = tc_filename( $path );
	}

	// Check whether this is a FSE theme by searching for an index.html block template.
	if ( ! in_array( 'block-templates/index.html', $other_filenames, true ) ) {
		return false;
	}

	// Remove theme checks that do not apply to FSE themes.
	foreach ( $themechecks as $key => $check ) {
		if ( $check instanceof File_Checks
			|| $check instanceof TagCheck
		) {
			unset( $themechecks[ $key ] );
		}
	}

	// Add FSE specific checks.
	$themechecks[] = new FSE_Required_Files();
}
