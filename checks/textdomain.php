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

		$checks = array(
		'/[\s|\(]_[e|_]\s?\([^,|;]*\s?,\s?[\'|"]([^\'|"]*)[\'|"]\s?\)/' => sprintf(__('Text domain should match theme slug: <strong>%1$s</strong>', 'theme-check'), $themename ),
		'/[\s|\(]_x\s?\([^,]*\s?,\s[^\'|"]*[\'|"][^\'|"]*[\'|"],\s?[\'|"]([^\'|"]*)[\'|"]\s?\)/' => sprintf(__('Text domain should match theme slug: <strong>%1$s</strong>', 'theme-check'), $themename )
		 );
		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match_all( $key, $phpfile, $matches ) ) {
					foreach ($matches[0] as $count => $domaincheck) {
						if ( preg_match( '/[\s|\(]_[e|_]\s?\(\s?[\'|"][^\'|"]*[\'|"]\s?\)/', $domaincheck ) )
							unset( $matches[1][$count] ); //filter out false positives
					}
					$filename = tc_filename( $php_key );
					$count = 0;
					while ( isset( $matches[1][$count] ) ) {
						if ( $matches[1][$count] !== $themename ) {
							$error = tc_grep( $matches[0][$count], $php_key );
							if ( $matches[1][$count] === 'twentyten' || $matches[1][$count] === 'twentyeleven' ):
								$this->error[] = sprintf( '<span class=\'tc-lead tc-recommended\'>' . __( 'RECOMMENDED', 'theme-check' ) . '</span>: '. __( 'Text domain problems in <strong>%1$s</strong>. The %2s text domain is being used!%3$s', 'theme-check' ), $filename, $matches[1][$count], $error );
							else:
							if ( defined( 'TC_TEST' ) && strpos( strtolower( $themename ), $matches[1][$count] ) === false ) {
								$error = tc_grep( $matches[0][$count], $php_key );
								$this->error[] = sprintf( '<span class=\'tc-lead tc-recommended\'>' . __( 'RECOMMENDED', 'theme-check' ) . '</span>: '. __( 'Text domain problems in <strong>%1$s</strong>. %2$s You are using: <strong>%3s</strong>%4$s', 'theme-check' ), $filename, $check, $matches[1][$count], $error );
							}
							endif;
						}
					$count++;
					} //end while
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new TextDomainCheck;