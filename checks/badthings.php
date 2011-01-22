<?php
class Bad_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		$checks = array(
			'/[\s|]eval\s*\([^\$|\'](.){25}/i' => __( 'eval() is not allowed.', 'theme-check' ),
			'/\s?(popen|proc_open|[^_]exec|shell_exec|system|passthru)\(/' => __( 'PHP sytem calls should be disabled by server admins anyway!', 'theme-check' ),
			'/\s?ini_set\(/' => __( 'Themes should not change server PHP settings', 'theme-check' ),
			'/base64_decode/' => __( 'base64_decode() is not allowed', 'theme-check' ),
			'/base64_encode/' => __( 'base64_encode() is not allowed', 'theme-check' ),
			'/uudecode/ims' => __( 'uudecode() is not allowed', 'theme-check' ),
			'/str_rot13/ims' => __( 'str_rot13() is not allowed', 'theme-check' ),
			'/cx=[0-9]{21}:[a-z0-9]{10}/' => __( 'Google search code detected', 'theme-check' ),
			'/pub-[0-9]{16}/i' => __( 'Googe advertising code detected', 'theme-check' )
			);

		$grep = '';

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
			checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = rtrim( $matches[0],'(' );
					$grep = tc_grep( $error, $php_key );
					$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: Found <strong>{$error}</strong> in the file <strong>{$filename}</strong>. {$check}.{$grep}";
					$ret = false;
				}
			}
		}


		$checks = array(
			'/cx=[0-9]{21}:[a-z0-9]{10}/' => __( 'Google search code detected', 'theme-check' ),
			'/pub-[0-9]{16}/' => __( 'Googe advertising code detected', 'theme-check' )
			);

		foreach ( $other_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = rtrim( $matches[0],'(' );
					$grep = tc_grep( $error, $php_key );
					$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: Found <strong>{$error}</strong> in the file <strong>{$filename}</strong>. {$check}.{$grep}";
					$ret = false;
				}
			}
		}
		return $ret;
	}
	function getError() { return $this->error; }
}
$themechecks[] = new Bad_Checks;