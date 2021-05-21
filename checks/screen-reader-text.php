<?php
class Screen_Reader_Text implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$css = implode( ' ', $css_files );
		$ret = true;

		$checks = array(
			'\.screen-reader-text' => __( '<strong>.screen-reader-text</strong> css class is needed in your theme CSS. See <a href="http://codex.wordpress.org/CSS#WordPress_Generated_Classes">the Codex</a> for an example implementation.', 'theme-check' ),
		);

		foreach ( $checks as $key => $check ) {
			checkcount();
			if ( ! preg_match( '/' . $key . '/i', $css, $matches ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span> %s',
					__( 'REQUIRED', 'theme-check' ),
					$check
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
$themechecks[] = new Screen_Reader_Text();
