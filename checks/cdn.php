<?php
/**
 * Checks for resources being loaded from CDNs.
 */

class CDNCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;
		$php_code = implode( ' ', $php_files );
		$css_code = implode( ' ', $css_files );
		$all_code = $php_code . ' ' . $css_code;

		checkcount();

		$cdn_list = array(
			'bootstrap-maxcdn'      => 'maxcdn.bootstrapcdn.com',
			'bootstrap-netdna'      => 'netdna.bootstrapcdn.com',
			'bootstrap-stackpath'   => 'stackpath.bootstrapcdn.com',
			'fontawesome'           => 'kit.fontawesome.com',
			'googlecode'            => 'googlecode.com/svn/',
			'oss.maxcdn'            => 'oss.maxcdn.com',
			'jquery'                => 'code.jquery.com/jquery-',
			'aspnetcdn'             => 'aspnetcdn.com',
			'cloudflare'            => 'cloudflare.com',
			'keycdn'                => 'keycdn.com',
			'pxgcdn'                => 'pxgcdn.com',
			'vimeocdn'              => 'vimeocdn.com',  //usually in JS files
		);

		foreach( $cdn_list as $cdn_slug => $cdn_url ) {
			if ( false !== strpos( $all_code, $cdn_url ) ) {
				$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED','theme-check' ) . '</span>: ' . sprintf( __( 'Found the URL of a CDN in the code: %s. You should not load CSS or Javascript resources from a CDN, please bundle them with the theme.', 'theme-check' ), '<code>' . esc_html( $cdn_url ) . '</code>' );
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CDNCheck;
