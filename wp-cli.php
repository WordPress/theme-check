<?php
if ( !function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

require_once dirname(__FILE__).'/checkbase.php';

class Theme_Check_Commands extends WP_CLI_Command {
	/**
	 * Theme check.
	 *
	 * ## OPTIONS
	 *
	 * [<theme>]
	 * : Theme name. Default: current theme
	 *
	 * [--format=<format>]
	 * : Accepted values: text, json. Default: text
	 *
	 * [--output=<output>]
	 * : Accepted values: stdout, stderr. Default: stdout
	 *
	 * ## EXAMPLES
	 *
	 * wp theme-check twentyfourteen
	 */
	function __invoke($args, $assoc_args) {
		// theme exists ?
		$theme = isset($args[0]) ? $args[0] : get_stylesheet();
		if ( empty($theme) || !wp_get_theme($theme)->exists() ) {
	  		WP_CLI::error(sprintf('Theme not found "%s".', $theme));
	  		exit;
		}

		global $themechecks, $checkcount, $data, $themename;
		$themename = $theme;
		$theme = get_theme_root( $theme ) . "/$theme";
		$files = listdir( $theme );
		$data = tc_get_theme_data( $theme . '/style.css' );
		if ( $data[ 'Template' ] ) {
			// This is a child theme, so we need to pull files from the parent, which HAS to be installed.
			$parent = get_theme_root( $data[ 'Template' ] ) . '/' . $data['Template'];
			if ( ! tc_get_theme_data( $parent . '/style.css' ) ) { // This should never happen but we will check while were here!
				WP_CLI::error( sprintf(
					__('Parent theme <strong>%1$s</strong> not found! You have to have parent AND child-theme installed!', 'theme-check'),
					$data[ 'Template' ] ) );
				exit;
			}
			$parent_data = tc_get_theme_data( $parent . '/style.css' );
			$themename = basename( $parent );
			$files = array_merge( listdir( $parent ), $files );
		}

		if ( !$files ) {
			WP_CLI::error(sprintf('Not found : theme [%s]', $theme));
			exit;
		}

		$php = $css = $other = array();
		foreach( $files as $key => $filename ) {
			switch ( substr( $filename, -4 ) ) {
			case '.php':
				$php[$filename] = php_strip_whitespace( $filename );
				break;
			case '.css':
				$css[$filename] = file_get_contents( $filename );
				break;
			default:
				$other[$filename] = ( ! is_dir($filename) ) ? file_get_contents( $filename ) : '';
			}
		}

		// run the checks
		$success = run_themechecks($php, $css, $other);

		$errors = array();
		foreach ($themechecks as $check) {
			if ($check instanceof themecheck) {
				$error = $check->getError();
				$error = (array) $error;
				if (!empty($error)) {
					$errors = array_unique( array_merge( $error, $errors ) );
				}
			}
		}

		$error_counts = array();
		if ( !empty($errors) ) {
			foreach( $errors as $value ) {
				$value = explode(':', $value, 2);
				if ( !isset($value[1]) ) {
					continue;
				}
				$error_kind = trim(strip_tags($value[0]));
				if ( !isset($error_counts[$error_kind]) ) {
					$error_counts[$error_kind] = array();
				}
				$error_counts[$error_kind][] = trim($value[1]);
			}
		}

		$show_msg = function($text, $hundle = 'stdout'){
			if ( $hundle !== 'stderr' ) {
				echo $text;
			} else {
				fputs(STDERR, $text);
			}
		};

		$format = strtolower(isset($assoc_args['format']) ? $assoc_args['format'] : 'text');
		$output = strtolower(isset($assoc_args['output']) ? $assoc_args['output'] : 'stderr');
		switch ($format) {
		case 'json':
			$errors = array('Meta' => (array)$data, 'Errors' => $error_counts);
			$show_msg(json_encode($errors)."\n", $output);
			break;
		
		default:
			$show_msg("*** Theme Information ***\n", $output);
			foreach ( (array)$data as $key => $value ) {
				$show_msg(sprintf( '[%s] : %s'."\n", $key, !is_array($value) ? $value : implode(',', $value) ), $output);
			}

			$show_msg("\n*** Theme Errors ***\n", $output);
			foreach ( $error_counts as $kind => $value ) {
				foreach ( $value as $msg ) {
					$show_msg(sprintf('[%s] %s'."\n", $kind, trim(strip_tags($msg,'<a>')) ), $output);
				}
			}

			$show_msg("\n*** Error Count ***\n", $output);
			foreach ( $error_counts as $key => $count ) {
				$show_msg(sprintf('[%s]'."\t".'%d'."\n", $key, count($count)),$output);
			}
			$show_msg(sprintf('[Summary]'."\t".'%d'."\n", count($errors)),$output);
			break;
		}
		exit( count($errors) > 0 ? 1 : 0 );
	}
}
WP_CLI::add_command('theme-check', 'Theme_Check_Commands');
