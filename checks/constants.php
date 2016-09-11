<?php

class Constants implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array(
			'STYLESHEETPATH'      => 'get_stylesheet_directory()',
			'TEMPLATEPATH'        => 'get_template_directory()',
			'PLUGINDIR'           => 'WP_PLUGIN_DIR',
			'MUPLUGINDIR'         => 'WPMU_PLUGIN_DIR',
			'HEADER_IMAGE'        => 'add_theme_support( \'custom-header\' )',
			'NO_HEADER_TEXT'      => 'add_theme_support( \'custom-header\' )',
			'HEADER_TEXTCOLOR'    => 'add_theme_support( \'custom-header\' )',
			'HEADER_IMAGE_WIDTH'  => 'add_theme_support( \'custom-header\' )',
			'HEADER_IMAGE_HEIGHT' => 'add_theme_support( \'custom-header\' )',
			'BACKGROUND_COLOR'    => 'add_theme_support( \'custom-background\' )',
			'BACKGROUND_IMAGE'    => 'add_theme_support( \'custom-background\' )',
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( '/[\s|\'|\"]' . $key . '(?:\'|"|;|\s)/', $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = ltrim( rtrim( $matches[0], '(' ), '\'"' );
					$grep = tc_grep( $error, $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('%1$s was found in the file %2$s. Use %3$s instead.%4$s', 'theme-check'), '<strong>' . $error . '</strong>', '<strong>' . $filename . '</strong>', '<strong>' . $check . '</strong>', $grep );
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Constants;
