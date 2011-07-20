<?php
class File_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$filenames = array();

		foreach ( $php_files as $php_key => $phpfile ) {
			array_push( $filenames, strtolower( basename( $php_key ) ) );
		}
		foreach ( $other_files as $php_key => $phpfile ) {
			array_push( $filenames, strtolower( basename( $php_key ) ) );
		}
		foreach ( $css_files as $php_key => $phpfile ) {
			array_push( $filenames, strtolower( basename( $php_key ) ) );
		}
		$blacklist = array(
				'thumbs.db' => __( 'Windows thumbnail store', 'themecheck' ),
				'desktop.ini' => __( 'windows system file', 'themecheck' ),
				'project.properties' => __( 'NetBeans Project File', 'themecheck' ),
				'project.xml' => __( 'NetBeans Project File', 'themecheck' ),
				'\.kpf' => __( 'Komodo Project File', 'themecheck' ),
				'^\.+[a-zA-Z0-9]' => __( 'Hidden Files or Folders', 'themecheck' ),
				'php.ini' => __( 'PHP server settings file', 'themecheck' ),
				'dwsync.xml' => __( 'Dreamweaver project file', 'themecheck' ),
				'error_log' => __( 'PHP error log', 'themecheck' ),
				'web.config' => __( 'Server settings file', 'themecheck' )
				);

		$musthave = array( 'index.php', 'comments.php', 'style.css' );
		$rechave = array( 'readme.txt' => __( 'Please see <a href="http://codex.wordpress.org/Theme_Review#Theme_Documentation">Theme_Documentation</a> for more information.', 'themecheck' ) );

		checkcount();

		foreach( $blacklist as $file => $reason ) {
			if ( $filename = preg_grep( '/' . $file . '/', $filenames ) ) {
				$error = implode( array_unique( $filename ), ' ' );
				$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: <strong>{$error}</strong> {$reason} found.";
				$ret = false;
			}
		}

		foreach( $musthave as $file ) {
			if ( !in_array( $file, $filenames ) ) {
				$this->error[] = __( "<span class='tc-lead tc-warning'>WARNING</span>: could not find the file <strong>{$file}</strong> in the theme.", "themecheck" );
				$ret = false;
			}
		}

		foreach( $rechave as $file => $reason ) {
			if ( !in_array( $file, $filenames ) ) {
				$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: could not find the file <strong>{$file}</strong> in the theme. {$reason}", "themecheck" );
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new File_Checks;