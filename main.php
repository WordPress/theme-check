<?php
function check_main() {
global $themechecks;

$files = listdir(TEMPLATEPATH);

		if ($files) {
				foreach($files as $key => $filename) {

				if (substr($filename, -4) == '.php') {
					$php[$filename] = file_get_contents($filename);
				}
				else if (substr($filename, -4) == '.css') {
					$css[$filename] = file_get_contents($filename);
				}
				else {
					$other[$filename] = file_get_contents($filename);
				}
			}

			$failed = false;
			foreach($themechecks as $check) {
				if ($check instanceof themecheck) {
					if (! $check->check($php, $css, $other) ) {
						$failed = true;
					}
				}
			}

			// second loop, to display the errors
			$plugins = get_plugins();
			global $checkcount;
			$version = explode('.', $plugins['theme-check/theme-check.php']['Version']);
			echo 'Guidelines Version: <strong>'. $version[0] . '</strong> Plugin revision: <strong>'. $version[1] .'</strong><br />';
			echo $checkcount . ' checks ran against <strong> ' . get_option('template') . '</strong><br>';
			if (!defined('WP_DEBUG') || WP_DEBUG == false ) echo '<span><strong>WP_DEBUG is not enabled!</strong> Please test your theme with debug enabled before you upload!</span>';
			// display the errors. Each checker class can return an array of strings as errors
			//echo '<br>Error List:<br>';
			//echo '<ul>';
$dos2unix = array();
$deprecated = array();
$required = array();
$cssneeded = array();
$cssoptional = array();
$critical = array();
$short = array();
$recommended = array();
$info = array();
			foreach ($themechecks as $check) {
				if ($check instanceof themecheck) {
					$error = $check->getError();

					$error = (array) $error;

					if (!empty($error)) {
						foreach ($error as $e) {
//if (preg_match('/DOS2UNIX/',$e)) { $e = str_replace('DOS2UNIX','',$e); array_push($dos2unix, $e); }
if (preg_match('/DEPRECATED/',$e)) { $e = str_replace('DEPRECATED','',$e); array_push($deprecated, $e); }
if (preg_match('/REQUIRED/',$e)) { $e = str_replace('REQUIRED','',$e); array_push($required, $e); }
if (preg_match('/CSSNEEDED/',$e)) { $e = str_replace('CSSNEEDED','',$e); array_push($required, $e); }
if (preg_match('/CSSOPTIONAL/',$e)) { $e = str_replace('CSSOPTIONAL','',$e); array_push($cssoptional, $e); }
if (preg_match('/CRITICAL/',$e)) { $e = str_replace('CRITICAL','',$e); array_push($critical, $e); }
if (preg_match('/SHORT/',$e)) { $e = str_replace('SHORT','',$e); array_push($short, $e); }
if (preg_match('/RECOMMENDED/',$e)) { $e = str_replace('RECOMMENDED','',$e); array_push($recommended, $e); }
if (preg_match('/INFO/',$e)) { $e = str_replace('INFO','',$e); array_push($info, $e); }
						}
					}
				}
			}
			if ($deprecated || $required || $critical || $short) {
				echo "<br /><h1>One or more errors were found.</h1>";
			} else {
				echo "<h2>Theme passed all the tests!</h2>";
			}
if ($critical) {
echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
echo '<ul>';
foreach($critical as $error){
echo '<li><span style="color:red">Critical: </span>'.$error.'</li>';
}
echo '</ul>';
echo '</div>';
}

if ($deprecated) {
echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
echo '<ul>';
foreach($deprecated as $error){
echo '<li><span style="color:red">Deprecated: </span>'.$error.'</li>';
}
echo '</ul>';
echo '</div>';
}

if ($required) {
echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
echo '<ul>';
foreach($required as $error){

echo '<li><span style="color:red">Required: </span>'.$error.'</li>';
}
echo '</ul>';
echo '</div>';
}

if ($recommended) {
echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
echo '<ul>';
foreach($recommended as $error){
echo '<li><span style="color:blue">Recommended: </span>'.$error.'</li>';
}
echo '</ul>';
echo '</div>';
}

if ($cssoptional) {
echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
echo '<ul>';
foreach($cssoptional as $error){
echo '<li><span style="color:green">Optional: </span>'.$error.'</li>';
}
echo '</ul>';
echo '</div>';
}

if ($info) {
echo '<div style="padding:20px 0;border-top:1px solid #ccc;"';
echo '<ul>';
foreach($info as $error){
echo '<li><span style="color:green">Info: </span>'.$error.'</li>';
}
echo '</ul>';
echo '</div>';
}
		}
	}


Function listdir($start_dir='.') {

  $files = array();
  if (is_dir($start_dir)) {
    $fh = opendir($start_dir);
    while (($file = readdir($fh)) !== false) {
      # loop through the files, skipping . and .., and recursing if necessary
      if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
      $filepath = $start_dir . '/' . $file;
      if ( is_dir($filepath) )
        $files = array_merge($files, listdir($filepath));
      else
        array_push($files, $filepath);
    }
    closedir($fh);
  } else {
    # false if the function was called with an invalid non-directory argument
    $files = false;
  }

  return $files;

}
function checkcount() {
global $checkcount;
$checkcount++;
}