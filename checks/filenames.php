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
		$blacklist = array(
				'thumbs.db' => __( 'Windows thumbnail store', 'theme-check' ),
				'desktop.ini' => __( 'windows system file', 'theme-check' ),
				'\.kpf' => __( 'Komodo Project File', 'theme-check' ),
				'^\..*' => __( 'Hidden File', 'theme-check' ),
				'php.ini' => __( 'PHP server settings file', 'theme-check' ),
				'dwsync.xml' => __( 'Dreamweaver project file', 'theme-check' )
				);

		$musthave = array( 'index.php', 'comments.php', 'screenshot.png', 'style.css' );
		$rechave = array( 'readme.txt' => __( ' Please see <a href="http://codex.wordpress.org/Theme_Review#Theme_Documentation">Theme_Documentation</a> for more information.', 'theme-check' ) );

		checkcount();

		foreach( $blacklist as $file => $reason ) {
			if ( $filename = preg_grep( '/' . $file . '/', $filenames ) ) {
				$error = implode($filename, ' ');
				$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: <strong>{$error}</strong> {$reason} found.";
				$ret = false;
			}
		}

		foreach( $musthave as $file ) {
			if ( !in_array( $file, $filenames ) ) {
				$this->error[] = __( "<span class='tc-lead tc-warning'>WARNING</span>: could not find the file <strong>{$file}</strong> in the theme.", "theme-check" );
				$ret = false;
			}
		}

		foreach( $rechave as $file => $reason ) {
			if ( !in_array( $file, $filenames ) ) {
				$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: could not find the file <strong>{$file}</strong> in the theme.{$reason}", "theme-check" );
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new File_Checks;