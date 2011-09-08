<?php

class Constants implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array(
			'STYLESHEETPATH' => 'get_stylesheet_directory()',
			'TEMPLATEPATH' => 'get_template_directory()',
			'PLUGINDIR' => 'WP_PLUGIN_DIR',
			'MUPLUGINDIR' => 'WPMU_PLUGIN_DIR'
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( '/[\s|]' . $key . '/', $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = ltrim( rtrim( $matches[0], '(' ) );
					$grep = tc_grep( $error, $php_key );
					$this->error[] = "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: <strong>{$error}</strong> was found in the file <strong>{$filename}</strong>. Use <strong>{$check}</strong> instead.{$grep}";
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Constants;