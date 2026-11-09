<?php
/**
 * Regression cases for machine identifiers in the WordPress spelling check.
 */

interface themecheck {}

function checkcount() {}
function tc_filename( $path ) { return $path; }
function __( $text, $domain ) { return $text; }

require dirname( __DIR__ ) . '/checks/class-wordpress-spelling-check.php';

$cases = array(
	'issue 505 PHP array identifiers' => array(
		'path'     => 'functions.php',
		'content'  => "<?php\n\$networks = [\n    'wordpress' => [\n        'value' => 'wordpress',\n        'label' => 'WordPress',\n    ],\n];\n",
		'expected' => true,
	),
	'lowercase display text in PHP' => array(
		'path'     => 'functions.php',
		'content'  => "<?php echo 'wordpress';\n",
		'expected' => false,
	),
	'unrelated value field remains checked' => array(
		'path'     => 'functions.php',
		'content'  => "<?php\n\$field = [ 'value' => 'wordpress' ];\n",
		'expected' => false,
	),
	'other network value remains checked' => array(
		'path'     => 'functions.php',
		'content'  => "<?php\n\$networks = [ 'other' => [ 'value' => 'wordpress' ] ];\n",
		'expected' => false,
	),
	'lowercase translated text in PHP' => array(
		'path'     => 'functions.php',
		'content'  => "<?php echo __( 'wordpress', 'theme' );\n",
		'expected' => false,
	),
	'lowercase display text in HTML' => array(
		'path'     => 'template.html',
		'content'  => '<p>wordpress</p>',
		'expected' => false,
	),
	'incorrect display capitalization' => array(
		'path'     => 'functions.php',
		'content'  => "<?php echo 'Wordpress';\n",
		'expected' => false,
	),
);

$failed = 0;
foreach ( $cases as $name => $case ) {
	$check  = new WordPress_Spelling_Check();
	$result = $check->check(
		'functions.php' === $case['path'] ? array( $case['path'] => $case['content'] ) : array(),
		array(),
		'functions.php' !== $case['path'] ? array( $case['path'] => $case['content'] ) : array()
	);
	if ( $case['expected'] !== $result ) {
		++$failed;
		fwrite( STDERR, "FAIL: {$name}; expected " . ( $case['expected'] ? 'pass' : 'violation' ) . "\n" );
	} else {
		echo "PASS: {$name}\n";
	}
}

exit( $failed ? 1 : 0 );
