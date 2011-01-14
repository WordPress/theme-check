<?php
class File_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;

		$filenames = array();

		foreach ($php_files as $php_key => $phpfile) {
			array_push( $filenames, strtolower( basename( $php_key ) ) );
		}
		foreach ($other_files as $php_key => $phpfile) {
			array_push( $filenames, strtolower( basename( $php_key ) ) );
		}
		foreach ($css_files as $php_key => $phpfile) {
			array_push( $filenames, strtolower( basename( $php_key ) ) );
		}
		$blacklist = array( '.kpf' => 'Komodo Project File' );
		$musthave = array( 'index.php', 'comments.php', 'screenshot.png', 'style.css' );
		$rechave = array( 'readme.txt' => __( ' Please see <a href="http://codex.wordpress.org/Theme_Review#Theme_Documentation">Theme_Documentation</a> for more information.', 'themecheck' ) );

		checkcount();

		foreach( $blacklist as $file => $reason ) {
			if ( preg_grep( '/' . preg_quote( $file ) . '/', $filenames ) ) {
				$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: {$reason} found.";
				$ret = false;
			}
		}

		foreach( $musthave as $file ) {
			if ( !in_array( $file, $filenames ) ) {
				$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: could not find the file <strong>{$file}</strong> in the theme.";
				$ret = false;
			}
		}

		foreach( $rechave as $file => $reason ) {
			if ( !in_array( $file, $filenames ) ) {
				$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: could not find the file <strong>{$file}</strong> in the theme.{$reason}", "themecheck" );
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new File_Checks;