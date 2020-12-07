<?php

class Check_URI implements themecheck {
	protected $error = array();

	protected $theme_data = array();

	function set_context( $data ) {
		if ( isset( $data['theme_data'] ) ) {
			$this->theme_data = $data['theme_data'];
		}
	}

	function check( $php_files, $css_files, $other_files ) {
		checkcount();
		$ret = true;

		if ( ! empty( $this->theme_data['AuthorURI'] ) && ! empty( $this->theme_data['URI'] ) ) {

			if ( strtolower( preg_replace( '/https?:\/\/|www./i', '', trim( $this->theme_data['URI'], '/' ) ) ) == strtolower( preg_replace( '/https?:\/\/|www./i', '', trim( $this->theme_data['AuthorURI'], '/' ) ) ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Your Theme URI and Author URI should not be the same.', 'theme-check' )
				);
				$ret           = false;
			}

			// We allow .org user profiles as Author URI, so only check the Theme URI. We also allow WordPress.com links.
			if (
				$this->theme_data['AuthorName'] != 'the WordPress team' &&
				( stripos( $this->theme_data['URI'], 'wordpress.org' ) || stripos( $this->theme_data['URI'], 'w.org' ) )
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
