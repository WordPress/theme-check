<?php
/**
 * Check that searchform.php is not included directly
 *
 * @package Theme Check
 */

/**
 * Check that searchform.php is not included directly.
 */
class Search_Form_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {
		$checks = array(
			'/searchform.php/' => __( 'Use <strong>get_search_form()</strong> instead of including searchform.php directly. Otherwise, the form can not be filtered.', 'theme-check' ),
		);

		foreach ( $php_files as $file_path => $file_contents ) {
			foreach ( $checks as $regex => $check_text ) {
				checkcount();
				if ( preg_match( $regex, $file_contents, $out ) ) {
					$grep          = tc_preg( $regex, $file_path );
					$this->error[] = sprintf(
						'<span class="tc-lead tc-warning">%s</span>: %s %s',
						__( 'WARNING', 'theme-check' ),
						sprintf(
							__( '<strong>searchform.php</strong> was found in %1$s. %2$s', 'theme-check' ),
							'<strong>' . tc_filename( $file_path ) . '</strong>',
							$check_text
						),
						$grep
					);
				}
			}

			$filename = tc_filename( $file_path );
			// This doesn't apply to searchform.php or WooCommerce product-searchform.php.
			if ( $filename === 'searchform.php' || $filename === 'product-searchform.php' ) {
				continue;
			}

			checkcount();

			// Checking for role="search" instead of form because it has a low risk of false positives.
			if ( false !== strpos( $file_contents, 'role="search"' ) ) {
				$grep          = tc_preg( $regex, $file_path );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s %s',
					__( 'WARNING', 'theme-check' ),
					sprintf(
						__( '<strong>role="search"</strong> was found in %1$s. %2$s', 'theme-check' ),
						'<strong>' . tc_filename( $file_path ) . '</strong>',
						__( 'Use <strong>get_search_form()</strong> instead of hard coding forms. Otherwise, the form can not be filtered.', 'theme-check' )
					),
					$grep
				);
			}
		}

		return true;
	}

	/**
	 * Get error messages from the checks.
	 *
	 * @return array Error message.
	 */
	public function getError() {
		return $this->error;
	}
}

$themechecks[] = new Search_Form_Check();
