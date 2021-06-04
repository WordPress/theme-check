<?php
/**
 * Check if post thumbnails are supported
 *
 * @package Theme Check
 */

/**
 * Check if post thumbnails are supported
 */
class Post_Thumbnail_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		// Combine all the php files into one string to make it easier to search.
		$php = implode( ' ', $php_files );
		checkcount();

		if ( strpos( $php, 'the_post_thumbnail' ) === false ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span> %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( 'No reference to <strong>the_post_thumbnail()</strong> was found in the theme. It is recommended that the theme implement this functionality instead of using custom fields for thumbnails.', 'theme-check' )
			);
		}

		if ( strpos( $php, 'post-thumbnails' ) === false ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span> %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( 'No reference to post-thumbnails was found in the theme. If the theme has a thumbnail like functionality, it should be implemented with <strong>add_theme_support( "post-thumbnails" )</strong> in the functions.php file.', 'theme-check' )
			);
		}

		return $ret;
	}

	/**
	 * Get error messages from the checks.
	 *
	 * @return array Error message.
	 */
	public function getError() {
		return $this->error;
	}
}

$themechecks[] = new Post_Thumbnail_Check();
