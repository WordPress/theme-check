<?php 
// main global to hold our checks
global $themechecks;
$themechecks = array();

// interface that all checks should implement
interface themecheck
{
	// should return true for good/okay/acceptable, false for bad/not-okay/unacceptable
	public function check( $php_files, $css_files, $other_files );
	
	// should return an array of strings explaining any problems found
	public function getError();
}

// load all the checks in the checks directory
$dir = 'checks';
foreach (glob(dirname(__FILE__). "/{$dir}/*.php") as $file) {
	include $file;
}

function run_themechecks($php, $css, $other) {
	global $themechecks;
	$pass = true;
	foreach($themechecks as $check) {
		if ($check instanceof themecheck) {
			$pass = $pass & $check->check($php, $css, $other);
		}
	}
	return $pass;
}

function display_themechecks() {
	global $themechecks;
	$errors = array();
	foreach ($themechecks as $check) {
		if ($check instanceof themecheck) {
			$error = $check->getError();
			$error = (array) $error;
			if (!empty($error)) {
				$errors = array_merge($error, $errors);
			}
		}
	}
	if (!empty($errors)) {
		rsort($errors);
		foreach ($errors as $e) {
			echo '<li>'.$e.'</li>';
		}
	}
}
