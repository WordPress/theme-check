<?php
/**
 * Checks for resources being loaded from CDNs.
 *
 * @package Theme Check
 */

/**
 * Checks for resources being loaded from CDNs.
 *
 * Checks for resources being loaded from CDNs. Usage of CDN is required to be removed.
 */
class CDN_Check implements themecheck {
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

		$ret      = true;
		$php_code = implode( ' ', $php_files );
		$css_code = implode( ' ', $css_files );
		$all_code = $php_code . ' ' . $css_code;

		checkcount();

		$cdn_list = array(
			'bootstrap-maxcdn'    => 'maxcdn.bootstrapcdn.com',
			'bootstrap-netdna'    => 'netdna.bootstrapcdn.com',
			'bootstrap-stackpath' => 'stackpath.bootstrapcdn.com',
			'fontawesome'         => 'kit.fontawesome.com',
			'googlecode'          => 'googlecode.com/svn/',
			'oss.maxcdn'          => 'oss.maxcdn.com',
			'jquery'              => 'code.jquery.com/jquery-',
			'aspnetcdn'           => 'aspnetcdn.com',
			'cloudflare'          => 'cloudflare.com',
			'keycdn'              => 'keycdn.com',
			'pxgcdn'              => 'pxgcdn.com',
			'vimeocdn'            => 'vimeocdn.com',  // Usually in JS files.
		);

		foreach ( $cdn_list as $cdn_slug => $cdn_url ) {
			if ( false !== strpos( $all_code, $cdn_url ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'Found the URL of a CDN in the code: %s. You should not load CSS or Javascript resources from a CDN, please bundle them with the theme.', 'theme-check' ),
						'<code>' . esc_html( $cdn_url ) . '</code>'
					)
				);
				$ret           = false;
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

$themechecks[] = new CDN_Check();
