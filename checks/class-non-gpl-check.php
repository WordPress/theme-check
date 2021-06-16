<?php
/**
 * Checks if the theme includes resoruces from websites that does not use a GPL compatible license.
 *
 * @package Theme Check
 */

/**
 * Checks if the theme includes resoruces from websites that does not use a GPL compatible license.
 */
class Non_GPL_Check implements themecheck {
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

		$ret  = true;
		$code = implode( ' ', $other_files ); // The references are usually in the readme.

		checkcount();

		$link_list = array(
			'unsplash.com'    => 'https://unsplash.com/license',
			'pixabay'         => 'https://pixabay.com/service/license/',
			'freeimages'      => 'https://www.freeimages.com/license',
			'photopin'        => 'https://www.vecteezy.com/licensing-agreement',
			'vecteezy'        => 'https://www.vecteezy.com/licensing-agreement',
			'splitshire'      => 'https://www.splitshire.com/licence/',
			'freepik'         => 'https://www.freepikcompany.com/legal',
			'flaticon'        => 'https://www.freepikcompany.com/legal',
			'pikwizard'       => 'https://pikwizard.com/standard-license',
			'stock.adobe'     => 'https://stock.adobe.com/license-terms',
			'elements.envato' => 'https://elements.envato.com/license-terms',
			'undraw.co'       => 'https://undraw.co/license',
		);

		foreach ( $link_list as $link_slug => $link_url ) {
			if ( false !== stripos( $code, $link_slug ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'Found a reference to %s. Assets from this website does not use a license that is compatible with GPL.', 'theme-check' ),
						'<code>' . esc_html( $link_slug ) . '</code>'
					),
					'<a href="' . esc_url( $link_url ) . '" target="_blank">' . __( 'View license (opens in a new window).', 'theme-check' ) . '</a>'
				);
			}
		}

		return $ret;
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

$themechecks[] = new Non_GPL_Check();
