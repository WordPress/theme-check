<?php

class IncludeCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$checks = array(
			'/(?<![a-z0-9_\'"])(?:require|include)(?:_once)?\s?[\'"\(]/i' => __( 'The theme appears to use include or require. If these are being used to include separate sections of a template from independent files, then <strong>get_template_part()</strong> should be used instead.', 'theme-check' )
		);

		foreach ( $php_files as $file_path => $file_content ) {
			foreach ( $checks as $check_regex => $check ) {
				checkcount();

				$filename = tc_filename( $file_path );
				// This doesn't apply to functions.php
				if ( $filename === 'functions.php' ) {
					continue;
				}

				if ( preg_match( $check_regex, $file_content, $matches ) ) {
					$grep          = tc_preg( '/(?<![a-z0-9_\'"])(?:require|include)(?:_once)?\s?[\'"\(]/i', $file_path );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-info">%s</span>: <strong>%s</strong> %s %s',
						__( 'INFO', 'theme-check' ),
						$filename,
						$check,
						$grep
					);
				}
			}
		}

		return true;
	}

	function getError() {
		return $this->error;
	}
}
$themechecks[] = new IncludeCheck();
