<?php
class Suggested implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;

		$checks = array(
			'/[\s|]get_bloginfo\((\s|)("|\')url("|\')(\s|)\)/m' => 'home_url()',
			'/[\s|]get_bloginfo\((\s|)("|\')wpurl("|\')(\s|)\)/m' => 'site_url()',
			'/[\s|]get_bloginfo\((\s|)("|\')stylesheet_directory("|\')(\s|)\)/m' => 'get_stylesheet_directory_uri()',
			'/[\s|]get_bloginfo\((\s|)("|\')template_directory("|\')(\s|)\)/m' => 'get_template_directory_uri()',
			'/[\s|]get_bloginfo\((\s|)("|\')template_url("|\')(\s|)\)/m' => 'get_template_directory_uri()',
			'/[\s|]get_bloginfo\((\s|)("|\')text_direction("|\')(\s|)\)/m' => 'is_rtl()',
			'/[\s|]get_bloginfo\((\s|)("|\')feed_url("|\')(\s|)\)/m' => 'get_feed_link( \'feed\' ) (where feed is rss, rss2, atom)',
			'/[\s|]bloginfo\((\s|)("|\')url("|\')(\s|)\)/m' => 'echo home_url()',
			'/[\s|]bloginfo\((\s|)("|\')wpurl("|\')(\s|)\)/m' => 'echo site_url()',
			'/[\s|]bloginfo\((\s|)("|\')stylesheet_directory("|\')(\s|)\)/m' => 'echo get_stylesheet_directory_uri()',
			'/[\s|]bloginfo\((\s|)("|\')template_directory("|\')(\s|)\)/m' => 'echo get_template_directory_uri()',
			'/[\s|]bloginfo\((\s|)("|\')template_url("|\')(\s|)\)/m' => 'echo get_template_directory_uri()',
			'/[\s|]bloginfo\((\s|)("|\')text_direction("|\')(\s|)\)/m' => 'is_rtl()',
			'/[\s|]bloginfo\((\s|)("|\')feed_url("|\')(\s|)\)/m' => 'echo get_feed_link( \'feed\' ) (where feed is rss, rss2, atom)',
			);

		foreach ($php_files as $php_key => $phpfile) {
			foreach ($checks as $key => $check) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = basename($php_key);
					$error = trim( esc_html( rtrim($matches[0],'(') ) );
					$grep = tc_grep( rtrim($matches[0],'('), $php_key);
					$this->error[] = "RECOMMENDED<strong>{$error}</strong> was found in the file <strong>{$filename}</strong>. Use <strong>{$check}</strong> instead.{$grep}";
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Suggested;
