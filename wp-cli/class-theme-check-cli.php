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
	 * default: cli
	 * options:
	 *   - cli
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
		$format = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'cli' );

		// Get the current theme
		$current_theme      = wp_get_theme();
		$current_theme_slug = $current_theme->get_stylesheet();

		// Use the provided theme slug if available, otherwise use the current theme
		$check_theme_slug = ! empty( $args[0] ) ? $args[0] : $current_theme_slug;

		// Get the theme
		$theme = wp_get_theme( $check_theme_slug );

		if ( ! $theme->exists() ) {
			if ( $format === 'json' ) {
				$json_output = array(
					'completed' => false,
					'result'    => "Error: Theme '{$check_theme_slug}' not found.",
					'messages'  => array(),
				);
				WP_CLI::line( wp_json_encode( $json_output, JSON_PRETTY_PRINT ) );
				return;
			}
			WP_CLI::error( "Theme '{$check_theme_slug}' not found." );
		}

		// Run the checks.
		$success = run_themechecks_against_theme( $theme, $check_theme_slug );

		if ( $format === 'json' ) {
			if ( ! $success ) {
				$json_output = array(
					'completed' => false,
					'result'    => "Error: Theme check failed for {$check_theme_slug}.",
					'messages'  => array(),
				);
				WP_CLI::line( wp_json_encode( $json_output, JSON_PRETTY_PRINT ) );
				return;
			}
			$this->display_themechecks_as_json( $check_theme_slug );
		} else {
			if ( ! $success ) {
				WP_CLI::error( "Theme check failed for {$check_theme_slug}." );
			}
			$this->display_themechecks_in_cli( $check_theme_slug );
		}
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
				return html_entity_decode( wp_strip_all_tags( $message ), ENT_QUOTES, 'UTF-8' );
			},
			$messages
		);

		return $processed_messages;
	}

	/**
	 * Display the theme checks in the CLI.
	 *
	 * @param string $slug The slug of the theme to display the checks for.
	 * @return void
	 */
	private function display_themechecks_in_cli( $slug ) {
		$processed_messages = $this->process_themecheck_messages();

		WP_CLI::success( "Theme check completed for {$slug}." );

		if ( empty( $processed_messages ) ) {
			WP_CLI::line( 'No issues found.' );
			return;
		}

		foreach ( $processed_messages as $message ) {
			WP_CLI::line( '' );
			WP_CLI::line( $message );
		}
	}

	/**
	 * Display the theme checks in JSON format.
	 *
	 * @param string $slug The slug of the theme to display the checks for.
	 * @return void
	 */
	private function display_themechecks_as_json( $slug ) {
		$processed_messages = $this->process_themecheck_messages();

		$json_output = array(
			'completed' => true,
			'result'    => "Theme check completed for {$slug}.",
			'messages'  => $processed_messages,
		);

		WP_CLI::line( wp_json_encode( $json_output, JSON_PRETTY_PRINT ) );
	}
}
