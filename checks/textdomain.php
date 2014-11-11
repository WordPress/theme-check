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

		$domains = array();
		$php = implode( ' ', $php_files );
		foreach ( $get_domain_regexs as $regex ) {
			if ( preg_match_all( $regex, $php, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ){
					if ( ! empty( $match[1] ) ) {
						$domains[] = $match[1];
					}
				}
			}
		}
		$domains = array_unique( $domains );
		if ( count( $domains ) > 2 ){
			$this->error[] = sprintf( '<span class=\'tc-lead tc-recommended\'>' . __( 'RECOMMENDED', 'theme-check' ) . '</span>: '. __( 'You should have 1 text domain for your theme, with an optional second for a framework. This theme is using %1$s: <strong>%2$s</strong>.', 'theme-check' ), count( $domains ), implode( ', ', $domains ) );
		}

		// If we don't have the tokenizer, just return this check.
		if ( ! function_exists( 'token_get_all' ) ) {
			return $ret;
		}

		$get_domain_regexs = array(
			'/[\s\(;]_[_e]\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?([^\)]*)\s?\)/',
			'/[\s\(]_x\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"][^\'"]*[\'"]\s?,\s?([^\)]*)\s?\)/',
			'/[\s\(]_n\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"][^\'"]*[\'"]\s?,\s?[$a-z\(\)0-9]*\s?,\s?([^\)]*)\s?\)/',
			'/[\s\(]_nx\s?\(\s?[\'"][^\'"]*[\'"]\s?,\s?[\'"][^\'"]*[\'"]\s?,\s?[$a-z\(\)0-9]*\s?,\s?[\'"][^\'"]*[\'"]\s?,\s?([^\)]*)\s?\)/',
		);

		foreach ( $php_files as $file_path => $file_contents ) {
			$file_name = basename( $file_path );
			foreach ( $get_domain_regexs as $regex ) {
				if ( preg_match_all( $regex, $file_contents, $matches, PREG_SET_ORDER ) ) {
					foreach ( $matches as $match ){
						$error = $match[0];
						$tokens = @token_get_all( '<?php '.$match[1].';' );
						if ( empty( $tokens ) ) {
							continue;
						}
						foreach ( $tokens as $token ) {
							if ( is_array( $token ) && in_array( $token[0], array( T_VARIABLE, T_CONST, T_STRING ) ) ) {
								$grep = tc_grep( $error, $file_path );
								$this->error[] = sprintf( '<span class=\'tc-lead tc-recommended\'>' . __( 'RECOMMENDED', 'theme-check' ) . '</span>: '. __( 'Text domain problems in <strong>%1$s</strong>.Text domain must be a string: <code>%2$s</code> is not a valid text domain. %3$s', 'theme-check' ), $file_name, trim( $match[1] ), $grep );
							}
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