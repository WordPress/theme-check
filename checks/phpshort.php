<?php
class PHPShortTagsCheck implements themecheck {

	const ALLOW_SHORT_ECHO = true;

	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$regex = (self::ALLOW_SHORT_ECHO) ? '/<\?(?!php|xml|=)/i' : '/<\?(\=?)(?!php|xml)/i';
		foreach ( $php_files as $php_key => $phpfile ) {
			checkcount();
			if ( preg_match( $regex, $phpfile ) ) {
				$filename = tc_filename( $php_key );
				$grep = tc_preg( $regex, $php_key );
				$this->error[] = sprintf('<span class="tc-lead tc-warning">'.__('WARNING','theme-check').'</span>: '.__('Found PHP short tags in file %1$s.%2$s', 'theme-check'), '<strong>' . $filename . '</strong>', $grep);
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new PHPShortTagsCheck;