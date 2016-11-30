<?php

class Access_Private implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$grep = '';

		$ret = true;

		$checks = array(
			'_cleanup_header_comment()',
			'_get_plugin_data_markup_translate()',
			'_transition_post_status()',
			'_wp_post_revision_fields()',
			'do_shortcode_tag()',
			'get_post_type_labels()',
			'preview_theme_ob_filter()',
			'preview_theme_ob_filter_callback()',
			'wp_get_sidebars_widgets()',
			'wp_get_widget_defaults()',
			'wp_set_sidebars_widgets()',
			'wp_unregister_GLOBALS()',
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $check ) {
				checkcount();

				if ( preg_match( '/(?<!function)[\s?]' . $check . '\s?\(/', $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = ltrim( rtrim( $matches[0], '(' ) );
					$grep = tc_grep( $error, $php_key );

					// Point out the private function.
					$error_msg = sprintf(
						__( 'Private function %1$s found in the file %2$s.', 'theme-check' ),
						'<strong>' . $error . '()</strong>',
						'<strong>' . $filename . '</strong>'
					);

					// Add the precise code match that was found.
					$error_msg .= $grep;

					// Add the finalized error message.
					$this->error[] = '<span class="tc-lead tc-required">' . __('REQUIRED','theme-check') . '</span>: ' . $error_msg;

					$ret = false;
				}
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Access_Private;
