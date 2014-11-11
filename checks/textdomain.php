<?php

class TextDomainCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		global $data, $themename;
		$ret = true;
		$error = '';
		checkcount();
		if ( $data['Name'] === 'Twenty Ten' || $data['Name'] === 'Twenty Eleven')
			return $ret;

		// Checks for a textdomain in __(), _e(), _x(), _n(), and _nx().
		$textdomain_regex = '/[\s\(]_x\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"][^\'"]*[\'"]\s?\)|[\s\(;]_[_e]\s?\(\s?[\'"][^\'"]*[\'"]\s?\)|[\s\(]_n\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"][^\'"]*[\'"]\s?,\s?[$a-z\(\)0-9]*\s?\)|[\s\(]_nx\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"][^\'"]*[\'"]\s?,\s?[$a-z\(\)0-9]*\s?,\s?[\'"][^\'"]*[\'"]\s?\)/';

		foreach ( $php_files as $file_path => $file_contents ) {
			$error = '';

			if ( preg_match_all( $textdomain_regex, $file_contents, $matches ) ) {
				$filename = tc_filename( $file_path );
				foreach ( $matches[0] as $match ) {
					$grep = tc_grep( ltrim( $match ), $file_path );
					preg_match( '/[^\s]*\s[0-9]+/', $grep, $line );
					$error .= ( ! strpos( $error, $line[0] ) ) ? $grep: '';
				}

				/* translators: 1: filename 2: grep results */
				$this->error[] = sprintf( "<span class='tc-lead tc-recommended'>" . __( 'RECOMMENDED', 'theme-check' ) . '</span>: ' . __( 'Text domain problems in <strong>%1$s</strong>. %2$s ', 'theme-check' ), $filename, $error );
			}
		}

		// Check if we have a textdomain in style.css.
		if ( empty( $data['Text Domain'] ) ){
			$this->error[] = sprintf( '<span class=\'tc-lead tc-recommended\'>%s</span> %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( 'Text domain is not defined in style.css.', 'theme-check' )
			);
		}

		$get_domain_regexs = array(
			'/[\s\(;]_[_e]\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"]([^\'"]*)[\'"]\s?\)/',
			'/[\s\(]_x\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"]([^\'"]*)[\'"]\s?\)/',
			'/[\s\(]_n\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"][^\'"]*[\'"]\s?,\s?[$a-z\(\)0-9]*\s?,\s?[\'"]([^\'"]*)[\'"]\s?\)/',
			'/[\s\(]_nx\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"][^\'"]*[\'"]\s?,\s?[$a-z\(\)0-9]*\s?,\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"]([^\'"]*)[\'"]\s?\)/',
		);

		foreach ( $php_files as $file_path => $file_contents ) {
			$file_name = basename( $file_path );
			foreach ( $get_domain_regexs as $regex ) {
				checkcount();
				if ( preg_match_all( $regex, $file_contents, $matches, PREG_SET_ORDER ) ) {
					foreach ( $matches as $match ){
						if ( strtolower( $data['Text Domain'] ) !== strtolower( $match[1] )
						  && strtolower( $themename ) !== strtolower( $match[1] )
						) {
							$grep = tc_grep( $match[0], $file_path );
							$this->error[] = sprintf( '<span class=\'tc-lead tc-recommended\'>' . __( 'RECOMMENDED', 'theme-check' ) . '</span>: '. __( 'Text domain problems in <strong>%1$s</strong>. You are using: <strong>%2$s</strong>. %3$s', 'theme-check' ), $file_name, $match[1], $grep );

						}
					}
				}
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new TextDomainCheck;