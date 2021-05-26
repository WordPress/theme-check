<?php
/**
 * Admin Menu check test
 *
 * @package Theme_Check
 */

use PHPUnit\Framework\TestCase;

/**
 * Test that will check the admin menu check functionality.
 */
class AdminMenuTest extends TestCase
{
	/**
	 * A single example test.
	 */
	public function test_admin_check_works() {
		// Setup the test. Add mocks, fakes...
		$php_files = [
			'../data/404.php' => ''
		];
		$css_files = [
			// put just one test file per test case, whether it shouldn't return an error or if it should.
		];
		$other_files = [
			// put just one test file per test case, whether it shouldn't return an error or if it should.
		];
		// Instantiate the class to test.
		$admin_check = new AdminMenu();
		// Run the method you want to check.
		$output = $admin_check->check( $php_files, $css_files, $other_files );
		// Do the assertions.
		$this->assertSame( 'Output that is expected or something like that', $output );
	}
}
