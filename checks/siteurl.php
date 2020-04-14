<?php
/**
 * Checks if site_url is used, and adds an info.
 */
class SiteUrlCheck implements themecheck {
	protected $error = array();

		function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		checkcount();

		foreach ( $php_files as $file_path => $file_content ) {

			$filename = tc_filename( $file_path );

			if ( preg_match( '/site_url/', $file_content, $matches ) ) {
				$this->error[] = sprintf( '<span class="tc-lead tc-info">' . __( 'INFO', 'theme-check' ) . '</span>: ' . __( 'site_url() or get_site_url() was found in %1$s. site_url() references the URL where the WordPress files are located. Use home_url() if the intention is to point to the site address (home page), and in the search form.', 'theme-check' ),
					'<strong>' . $filename . '</strong>'
				);
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new SiteUrlCheck();
