<?php

class DirectoriesCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$excluded_directories = array(
			'.git',
			'.svn',
			'.hg',
			'.bzr',
		);

		$ret = true;

		$all_filenames = array_merge(
			array_keys( $php_files ),
			array_keys( $css_files ),
			array_keys( $other_files )
		);

		foreach ( $all_filenames as $path ) {
			checkcount();

			$filename = basename( $path );

			if ( in_array( $filename, $excluded_directories, true ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					__( 'Please remove any extraneous directories like .git or .svn from the ZIP file before uploading it.', 'theme-check' )
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

$themechecks[] = new DirectoriesCheck();
