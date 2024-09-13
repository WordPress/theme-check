<?php

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	$parent_dir = dirname( __DIR__ );
	require_once $parent_dir . '/checkbase.php';
	require_once $parent_dir . '/main.php';
	WP_CLI::add_command( 'theme-check', 'Theme_Check_Command' );
}

class Theme_Check_Command extends WP_CLI_Command {
	/**
	 * Run a theme check on the specified theme or the current theme.
	 *
	 * ## OPTIONS
	 * [<theme>]
	 * : The slug of the theme to check. If not provided, checks the current theme.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - json
	 * ---
	 *
	 * ## EXAMPLES
	 *     # Check the current active theme
	 *     wp theme-check run
	 *
	 *     # Check a specific theme
	 *     wp theme-check run twentytwentyfour
	 *
	 *     # Check the current active theme and output results as JSON
	 *     wp theme-check run --format=json
	 *
	 *     # Check a specific theme and output results as JSON
	 *     wp theme-check run twentytwentyfour --format=json
	 *
	 * @param array $args       Indexed array of positional arguments.
	 * @param array $assoc_args Associative array of options.
	 * @return void
	 */
	public function run( $args, $assoc_args ) {
		$format = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );

		// Get the current theme
		$current_theme      = wp_get_theme();
		$current_theme_slug = $current_theme->get_stylesheet();

		// Use the provided theme slug if available, otherwise use the current theme
		$check_theme_slug = ! empty( $args[0] ) ? $args[0] : $current_theme_slug;

		// Get the theme
		$theme = wp_get_theme( $check_theme_slug );

		if ( ! $theme->exists() ) {
			WP_CLI::error( "Theme '{$check_theme_slug}' not found." );
		}

		// Run the checks
		$success = run_themechecks_against_theme( $theme, $check_theme_slug );
		$processed_messages = $this->process_themecheck_messages();

		// The validation value is a boolean, but if the format is not JSON, we want to return a string.
		$validation_value = $format === 'json'
			? true
			: "There are no required changes in the theme {$check_theme_slug}.";

		if ( ! $success ) {
			$validation_value = 
				// If the format is JSON, return false, otherwise return a message
				$format === 'json'
					? false
					: "There are required changes in the theme {$check_theme_slug}.";
		}

		$processed_messages[] = array(
			'type' => 'VALIDATION',
			'value' => $validation_value,
		);

		WP_CLI\Utils\format_items( $format, $processed_messages, array( 'type', 'value' ) );

		// Set the exit code based on $success
		WP_CLI::halt( $success ? 0 : 1 );
	}

	/**
	 * Process theme check messages.
	 *
	 * @return array Processed messages.
	 */
	private function process_themecheck_messages() {
		global $themechecks;
		$messages = array();

		foreach ( $themechecks as $check ) {
			if ( $check instanceof themecheck ) {
				$error = $check->getError();
				$error = (array) $error;
				if ( ! empty( $error ) ) {
					$messages = array_merge( $messages, $error );
				}
			}
		}

		$processed_messages = array_map(
			function( $message ) {
				if ( preg_match( '/<span[^>]*>(.*?)<\/span>(.*)/', $message, $matches ) ) {
					$key = $matches[1];
					$value = $matches[2];
				} else {
					$key = '';
					$value = $message;
				}

				$key   = wp_strip_all_tags( $key );
				$key   = html_entity_decode( $key, ENT_QUOTES, 'UTF-8' );
				$key   = rtrim( $key, ':' );

				$value = wp_strip_all_tags( $value );
				$value = html_entity_decode( $value, ENT_QUOTES, 'UTF-8' );
				$value = ltrim( $value, ': ' );

				return array(
					'type' => trim( $key ),
					'value' => trim( $value ),
				);
			},
			$messages
		);

		return $processed_messages;
	}
}
