<?php
class IncludedPlugins implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		$filenames = array();

		foreach ( $other_files as $other_key => $otherfile ) {
			array_push( $filenames, strtolower( basename( $other_key ) ) );
		}

		$blacklist = array(
			'\.zip'	=> __( 'Zipped Plugin', 'theme-check' ),
		);

		checkcount();

		foreach ( $blacklist as $file => $reason ) {
			if ( $filename = preg_grep( '/' . $file . '/', $filenames ) ) {
				$error = implode( array_unique( $filename ), ' ' );
				$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED','theme-check' ) . '</span>: ' . __( '<strong>Zip file found.</strong> Plugins are not allowed in themes. The zip file found was <em>%s</em>.', 'theme-check' ), $error );
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new IncludedPlugins;
