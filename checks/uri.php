<?php

class Check_URI implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files, $data ) {

		checkcount();
		$ret = true;

		if ( ! empty( $data['AuthorURI'] ) && ! empty( $data['URI'] ) ) {

			if ( strtolower( preg_replace( '/https?:\/\/|www./i', '', trim( $data['URI'], '/' ) ) ) == strtolower( preg_replace( '/https?:\/\/|www./i', '', trim( $data['AuthorURI'], '/' ) ) ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Your Theme URI and Author URI should not be the same.', 'theme-check' )
				);
				$ret           = false;
			}

			// We allow .org user profiles as Author URI, so only check the Theme URI. We also allow WordPress.com links.
			if (
				$data['AuthorName'] != 'the WordPress team' &&
				( stripos( $data['URI'], 'wordpress.org' ) || stripos( $data['URI'], 'w.org' ) )
			) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Using a WordPress.org Theme URI is reserved for official themes.', 'theme-check' )
				);
				$ret           = false;
			}
		}

		return $ret;
	}

	function getError() {
		return $this->error;
	}
}
$themechecks[] = new Check_URI();
