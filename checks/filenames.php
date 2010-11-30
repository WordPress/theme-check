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

		$musthave = array( 'index.php', 'comments.php', 'screenshot.png', 'style.css' );
		$rechave = array( 'readme.txt' );

		checkcount();		

		foreach( $musthave as $file ) {
			if ( !in_array( $file, $filenames ) ) {
				$this->error[] = "CRITICALcould not find the file <strong>{$file}</strong> in the theme.";
				$ret = false;
			}
		}

		foreach( $rechave as $file ) {
			if ( !in_array( $file, $filenames ) ) {
				$this->error[] = "RECOMMENDEDcould not find the file <strong>{$file}</strong> in the theme.";
				$ret = false;
			}
		}		

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new File_Checks;