<?php
class ThemeNameCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		checkcount();
		$ret = true;
		global $themename;

			$blacklist = array( 'twentyten',  'twentyeleven',  'twentytwelve',  'twentythirteen',  'twentyfourteen',  'twentyfifteen',  'twentysixteen',  'twentyseventeen',  'twentyeighteen',  'twentynineteen',  'twentytwenty'  );

			foreach ($blacklist as $key) {
   				 if ( stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), $key ) !== false) {
   				 	$this->error[] = '<span class="tc-lead tc-required">'. __('REQUIRED','theme-check'). '</span>: '. sprintf( __('Theme names in the Twenty* series are reserved for default themes. Found %1$s.', 'theme-check'), '<strong>' . $key . '</strong>');
   				 	$ret = false;
   				 }   	
  			}

  			if ( stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), "wordpress") !== false
  				|| stripos( strtolower( preg_replace( '/[^a-z]/', '', $themename ) ), "theme") !== false
  				) {
				$this->error[] = '<span class="tc-lead tc-required">'. __('REQUIRED','theme-check'). '</span>: ' . __( 'Theme names should not contain the words theme or WordPress.', 'theme-check' );
				$ret = false;
			}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new ThemeNameCheck;