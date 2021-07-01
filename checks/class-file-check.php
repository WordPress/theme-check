<?php
/**
 * Check for files that are not allowed and files that are required.
 *
 * @package Theme Check
 */

/**
 * Check for files that are not allowed and files that are required.
 */
class File_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

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
			'loco.xml',
			'phpcs.xml',
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
			'\.xml'               => __( 'XML file', 'theme-check' ),
			'\.sh'                => __( 'Shell script file', 'theme-check' ),
			'favicon\.ico'        => __( 'Favicon', 'theme-check' ),
		);

		$musthave = array( 'index.php', 'style.css', 'readme.txt' );

		checkcount();

		foreach ( $blocklist as $file => $reason ) {
			if ( $filename     = preg_grep( '/' . $file . '/', $filenames ) ) {
				$commons = array_intersect( $filename, $allowlist );
				foreach ( $commons as $common ) {
					if ( ( $allowed_key = array_search( $common, $filename ) ) !== false ) {
						unset( $filename[ $allowed_key ] );
					}
				}
				if ( empty( $filename ) ) {
					continue;
				}
				$error         = implode( ' ', array_unique( $filename ) );
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( '%1$s %2$s found. This file must not be in the production version of the theme.', 'theme-check' ),
						'<strong>' . $error . '</strong>',
						$reason
					)
				);
				$ret           = false;
			}
		}

		foreach ( $musthave as $file ) {
			if ( ! in_array( $file, $filenames ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( 'Could not find the file %s in the theme.', 'theme-check' ),
						'<strong>' . $file . '</strong>'
					)
				);
				$ret           = false;
			}
		}

		return $ret;
	}

	/**
	 * Get error messages from the checks.
	 *
	 * @return array Error message.
	 */
	public function getError() {
		return $this->error;
	}
}

$themechecks[] = new File_Check();
