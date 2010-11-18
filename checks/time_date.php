<?php

// search for some bad things
class Time_Date implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		// combine all the php files into one string to make it easier to search
	//	$php = implode(' ', $php_files);

		$ret = true;

		// things to check for
		$checks = array(
		//'/get_the_time\((\s|)["|\'][A-Za-z\s]+(\s|)["|\']\)/' => 'get_the_time( get_option( \'date_format\' ) )',
		'/[^get_]the_time\((\s|)["|\'][A-Za-z\s]+(\s|)["|\']\)/' => 'the_time( get_option( \'date_format\' ) )'
			);

		foreach ($php_files as $php_key => $phpfile) {
		foreach ($checks as $key => $check) {
		checkcount();
			if ( preg_match( $key, $phpfile, $matches ) ) {
			    $filename = basename($php_key);
				$error = trim( esc_html( rtrim($matches[0],'(') ) );
			//	$grep = tc_grep( rtrim($matches[0],'('), $php_key);
				$this->error[] = "INFOAt least one hard coded date was found in the file <strong>{$filename}</strong>.";
				$ret = false;
			}


		}

}
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Time_Date;
