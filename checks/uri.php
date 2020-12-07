<?php

class Check_URI implements themecheck {
	protected $error = array();

	protected $theme = array();

	function set_context( $data ) {
		if ( isset( $data['theme'] ) ) {
			$this->theme = $data['theme'];
		}
	}

	function check( $php_files, $css_files, $other_files ) {
		checkcount();
		$ret = true;

		if ( ! empty( $this->theme['AuthorURI'] ) && ! empty( $this->theme['URI'] ) ) {

			if ( strtolower( preg_replace( '/https?:\/\/|www./i', '', trim( $this->theme['URI'], '/' ) ) ) == strtolower( preg_replace( '/https?:\/\/|www./i', '', trim( $this->theme['AuthorURI'], '/' ) ) ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Your Theme URI and Author URI should not be the same.', 'theme-check' )
				);
				$ret           = false;
			}

			// We allow .org user profiles as Author URI, so only check the Theme URI. We also allow WordPress.com links.
			if (
				$this->theme['AuthorName'] != 'the WordPress team' &&
				( stripos( $this->theme['URI'], 'wordpress.org' ) || stripos( $this->theme['URI'], 'w.org' ) )
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
