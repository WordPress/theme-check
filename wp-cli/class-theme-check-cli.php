<?php

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    $parent_dir = dirname(__DIR__);
    require_once $parent_dir . '/checkbase.php';
    require_once $parent_dir . '/main.php';
    WP_CLI::add_command( 'theme-check', 'Theme_Check_CLI' );
}

class Theme_Check_CLI {
    /**
     * Run a theme check on the specified theme or the current theme.
     *
     * ## OPTIONS
     *
     * [<theme>]
     * : The slug of the theme to check. If not provided, checks the current theme.
     *
     * [--format=<format>]
     * : Output format. Accepts 'cli' or 'json'. Default: 'cli'.
     *
     * ## EXAMPLES
     *
     *     wp theme-check run
     *     wp theme-check run twentytwentyfour
     *     wp theme-check run --format=json
     *     wp theme-check run twentytwentyfour --format=json
     *
     */
    public function run( $args, $assoc_args ) {
        // Get the output format
        $format = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'cli' );
        if ( !in_array( $format, ['cli', 'json'] ) ) {
            WP_CLI::error( "Invalid format. Accepts 'cli' or 'json'." );
            return;
        }

        // Get the current theme
        $current_theme = wp_get_theme();
        $current_theme_slug = $current_theme->get_stylesheet();

        // Use the provided theme slug if available, otherwise use the current theme
        $check_theme_slug = $args[0] ?? $current_theme_slug;

        // Get the theme
        $theme = wp_get_theme( $check_theme_slug );

        if ( ! $theme->exists() ) {
            if ( $format === 'json' ) {
                $json_output = array(
                    'check-completed' => false,
                    'result' => "Error: Theme '{$check_theme_slug}' not found.",
                    'messages' => []
                );
                WP_CLI::log (wp_json_encode($json_output, JSON_PRETTY_PRINT) );
                return;
            }
            WP_CLI::error( "Theme '{$check_theme_slug}' not found." );
            return;
        }

        // Run the checks.
        $success = run_themechecks_against_theme( $theme, $check_theme_slug );

        if ( $format === 'json' ) {
            if ( ! $success ) {
                $json_output = array(
                    'check-completed' => false,
                    'result' => "Error: Theme check failed for {$slug}.",
                    'messages' => []
                );
                WP_CLI::log (wp_json_encode($json_output, JSON_PRETTY_PRINT) );
                return;
            }
            $this->display_themechecks_as_json( $check_theme_slug );
        } else {
            if ( ! $success ) {
                WP_CLI::error( "Theme check failed for {$slug}." );
                return;
            }
            $this->display_themechecks_in_cli( $check_theme_slug );
        }
    }

    /**
     * Process theme check messages.
     *
     * @return array Processed messages categorized by type.
     */
    private function process_themecheck_messages() {
        global $themechecks;
        $messages = array();
        $processed_messages = array(
            'errors' => array(),
            'warnings' => array(),
            'infos' => array(),
            'others' => array()
        );

        foreach ($themechecks as $check) {
            if ($check instanceof themecheck) {
                $error = $check->getError();
                $error = (array) $error;
                if (!empty($error)) {
                    $messages = array_merge($messages, $error);
                }
            }
        }

        foreach ($messages as $message) {
            $clean_message = html_entity_decode(strip_tags($message), ENT_QUOTES, 'UTF-8');

            if (strpos($clean_message, 'ERROR:') === 0) {
                $processed_messages['errors'][] = $clean_message;
            } elseif (strpos($clean_message, 'WARNING:') === 0) {
                $processed_messages['warnings'][] = $clean_message;
            } elseif (strpos($clean_message, 'INFO') === 0) {
                $processed_messages['infos'][] = $clean_message;
            } else {
                $processed_messages['others'][] = $clean_message;
            }
        }

        return $processed_messages;
    }

    /**
     * Display the theme checks in the CLI.
     *
     * @return void
     */
    private function display_themechecks_in_cli( $slug ) {
        $processed_messages = $this->process_themecheck_messages();

        WP_CLI::success("Theme check completed for {$slug}.");

        if (
            !empty($processed_messages['errors']) ||
            !empty($processed_messages['warnings']) ||
            !empty($processed_messages['infos']) ||
            !empty($processed_messages['others'])
        ) {
            foreach ($processed_messages['errors'] as $error) {
                WP_CLI::error(ltrim($error, 'ERROR: '), false);
            }
            foreach ($processed_messages['warnings'] as $warning) {
                WP_CLI::warning(ltrim($warning, 'WARNING: '));
            }
            foreach ($processed_messages['infos'] as $info) {
                WP_CLI::log( $info );
            }
            foreach ($processed_messages['others'] as $other) {
                WP_CLI::log("LOG: " . $other);
            }
        } else {
            WP_CLI::success("No issues found.");
        }
    }

    /**
     * Display the theme checks in JSON format.
     *
     * @return void
     */
    private function display_themechecks_as_json( $slug ) {
        $processed_messages = $this->process_themecheck_messages();

        $json_output = array(
            'check-completed' => true,
            'result' => "Theme check completed for {$slug}.",
            'messages' => $processed_messages
        );

        WP_CLI::log(wp_json_encode($json_output, JSON_PRETTY_PRINT));
    }
}