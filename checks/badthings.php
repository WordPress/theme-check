<?php
class Bad_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
		$ret = true;

		$checks = array(
			'/[\s|]eval\s*\([^\$|\'](.){25}/i' => 'eval() is not allowed.',
			'/base64_decode/ims' => 'base64_decode() is not allowed',
			'/uudecode/ims' => 'uudecode() is not allowed',
			'/str_rot13/ims' => 'str_rot13() is not allowed',
			'/[^_]unescape/ims' => 'unescape() is not allowed',
			'/cx=[0-9]{21}:[a-z0-9]{10}/ims' => 'Google search code detected',
			'/add_(admin|submenu|theme)_page\s?\x28.*,\s?[0-9]\s?,/i' => 'Please see <a href="http://codex.wordpress.org/Roles_and_Capabilities">Roles_and_Capabilities</a>',
			'/pub-[0-9]{16}/i' => 'Googe advertising code detected'
			);

		$grep = '';

		foreach ($php_files as $php_key => $phpfile) {
			foreach ($checks as $key => $check) {
			checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = rtrim($matches[0],'(');
					$grep = tc_grep( $error, $php_key);
					$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: Found <strong>{$error}</strong> in the file <strong>{$filename}</strong>. {$check}.{$grep}";
					$ret = false;
				}
			}
		}


		$checks = array(
			'/cx=[0-9]{21}:[a-z0-9]{10}/ms' => 'Google search code detected',
			'/pub-[0-9]{16}/' => 'Googe advertising code detected'
			);

		foreach ($other_files as $php_key => $phpfile) {
			foreach ($checks as $key => $check) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = rtrim($matches[0],'(');
					$grep = tc_grep( $error, $php_key);
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