<?php
/**
 * Check for "Tested up to" versioning
 *
 * @package Theme Check
 */

/**
 * Check for "Tested up to" versioning.
 * Does "Tested up to" include patch versions (e.g. 5.8.1)?
 * If so, recommend including major versions only (e.g. 5.8)
 *
 * See: https://developer.wordpress.org/themes/basics/main-stylesheet-style-css/#explanations
 */
class Version_Tested_Upto_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Theme information. Requires PHP,
	 *
	 * @var object $theme
	 */
	protected $theme;

	function set_context( $data ) {
		if ( isset( $data['theme'] ) ) {
			$this->theme = $data['theme'];
		}
	}

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

		$filepath   = $this->theme->get_stylesheet_directory() . '/style.css';
		$theme_data = get_file_data(
			$filepath,
			array(
				'TestedUpto' => 'Tested up to',
			)
		);

		if ( ! empty( $theme_data['TestedUpto'] ) ) {
			$req_tested_decimal_count = substr_count( $theme_data['TestedUpto'], '.' );
			if ( $req_tested_decimal_count > 1 ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-recommended">%s</span>: %s',
					__( 'RECOMMENDED', 'theme-check' ),
					__( '<strong>Tested up to</strong> is recommended to have major versions only (e.g. 5.8). Patch version is not needed (e.g. 5.8.1).', 'theme-check' )
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

$themechecks[] = new Version_Tested_Upto_Check();
