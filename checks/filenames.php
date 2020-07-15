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

		$allowlist = array(
			'wpml-config.xml',
			'loco.xml'
		);

		$blocklist = array(
			'thumbs\.db'          => __( 'Windows thumbnail store', 'theme-check' ),
			'desktop\.ini'        => __( 'windows system file', 'theme-check' ),
			'project\.properties' => __( 'NetBeans Project File', 'theme-check' ),
			'project\.xml'        => __( 'NetBeans Project File', 'theme-check' ),
			'\.kpf'               => __( 'Komodo Project File', 'theme-check' ),
			'^\.+[a-zA-Z0-9]'     => __( 'Hidden Files or Folders', 'theme-check' ),
			'php\.ini'            => __( 'PHP server settings file', 'theme-check' ),
			'dwsync\.xml'         => __( 'Dreamweaver project file', 'theme-check' ),
			'error_log'           => __( 'PHP error log', 'theme-check' ),
			'web\.config'         => __( 'Server settings file', 'theme-check' ),
			'\.sql'               => __( 'SQL dump file', 'theme-check' ),
			'__MACOSX'            => __( 'OSX system file', 'theme-check' ),
			'\.lubith'            => __( 'Lubith theme generator file', 'theme-check' ),
			'\.wie'               => __( 'Widget import file', 'theme-check' ),
			'\.dat'               => __( 'Customizer import file', 'theme-check' ),
			'phpcs\.xml\.dist'    => __( 'PHPCS file', 'theme-check' ),
			'phpcs\.xml'          => __( 'PHPCS file', 'theme-check' ),
			'\.xml'               => __( 'XML file', 'theme-check' ),
			'\.sh'                => __( 'Shell script file', 'theme-check' ),
			'postcss\.config\.js' => __( 'PostCSS config file', 'theme-check' ),
			'\.editorconfig.'     => __( 'Editor config file', 'theme-check' ),
			'\.stylelintrc\.json' => __( 'Stylelint config file', 'theme-check' ),
			'\.map'               => __( 'Map file', 'theme-check' ),
			'\.eslintrc'          => __( 'ES lint config file', 'theme-check' ),
			'favicon\.ico'        => __( 'Favicon', 'theme-check' ),
		);

		$musthave = array( 'index.php', 'style.css', 'readme.txt' );
		$rechave  = array();

		checkcount();

		foreach ( $blocklist as $file => $reason ) {
			if ( $filename     = preg_grep( '/' . $file . '/', $filenames ) ) {
				$commons       = array_intersect( $filename, $allowlist );
				foreach ( $commons as $common ) {
					if (( $allowed_key = array_search($common, $filename)) !== false) {
						unset( $filename[$allowed_key] );
					}
				}
				if ( empty( $filename ) ) {
					continue;
				}
				$error         = implode( ' ', array_unique( $filename ) );
				$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( '%1$s %2$s found. This file must not be in a theme.', 'theme-check' ), '<strong>' . $error . '</strong>', $reason );
				$ret           = false;
			}
		}

		foreach ( $musthave as $file ) {
			if ( ! in_array( $file, $filenames ) ) {
				$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( 'Could not find the file %s in the theme.', 'theme-check' ), '<strong>' . $file . '</strong>' );
				$ret           = false;
			}
		}

		foreach ( $rechave as $file => $reason ) {
			if ( ! in_array( $file, $filenames ) ) {
				$this->error[] = sprintf( '<span class="tc-lead tc-recommended">' . __( 'RECOMMENDED', 'theme-check' ) . '</span>: ' . __( 'Could not find the file %1$s in the theme. %2$s', 'theme-check' ), '<strong>' . $file . '</strong>', $reason );
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new File_Checks();
