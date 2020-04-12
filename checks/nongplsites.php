<?php
/**
 * Checks if the theme includes resoruces from websites that does not use a GPL compatible license.
 */
class NonGPLCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret  = true;
		$code = implode( ' ', $other_files ); // The references are usually in the readme.

		checkcount();

		$link_list = array(
			'unsplash'            => 'https://unsplash.com/license',
			'pixabay'             => 'https://pixabay.com/service/license/',
			'freeimages'          => 'https://www.freeimages.com/license',
			'photopin'            => 'http://photopin.com/faq',
			'splitshire'          => 'https://www.splitshire.com/licence/',
			'freepik'             => 'https://www.freepikcompany.com/legal',
			'flaticon'            => 'https://www.freepikcompany.com/legal',
			'pikwizard'           => 'https://pikwizard.com/standard-license',
			'stock.adobe'         => 'https://stock.adobe.com/license-terms',
			'elements.envato'     => 'https://elements.envato.com/license-terms',
			'undraw.co'           => 'https://undraw.co/licenses',
		);

		foreach ( $link_list as $link_slug => $link_url ) {
			if ( false !== stripos( $code, $link_slug ) ) {
				$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' .
				sprintf(
					__( 'Found a reference to %s. Images from this website does not use a license that is compatible with GPL.', 'theme-check' ),
					'<code>' . esc_html( $link_slug ) . '</code>'
				)
					. ' <a href="' . esc_url( $link_url ) . '" target="_blank">' . __( 'View license (opens in a new window).', 'theme-check' ) . '</a>';
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new NonGPLCheck;
