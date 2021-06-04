<?php
/**
 * Check if post pagination and navigation is supported
 *
 * @package Theme Check
 */

/**
 * Check if post pagination and navigation is supported
 */
class Post_Pagination_Check implements themecheck {
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

		// Combine all the php files into one string to make it easier to search.
		$php = implode( ' ', $php_files );
		checkcount();
		if (
			strpos( $php, 'posts_nav_link' ) === false &&
			strpos( $php, 'paginate_links' ) === false &&
			strpos( $php, 'the_posts_pagination' ) === false &&
			strpos( $php, 'the_posts_navigation' ) === false &&
			(
				strpos( $php, 'previous_posts_link' ) === false &&
				strpos( $php, 'next_posts_link' ) === false
			)
		) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span> %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( "The theme doesn't have post pagination code in it. Use <strong>posts_nav_link()</strong> or <strong>paginate_links()</strong> or <strong>the_posts_pagination()</strong> or <strong>the_posts_navigation()</strong> or <strong>next_posts_link()</strong> and <strong>previous_posts_link()</strong> to add post pagination.", 'theme-check' )
			);
		}

		return true;
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
$themechecks[] = new Post_Pagination_Check();
