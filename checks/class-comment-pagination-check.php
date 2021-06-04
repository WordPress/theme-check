<?php
/**
 * Checks if comment pagination is included
 *
 * @package Theme Check
 */

/**
 * Checks if comment pagination is included.
 *
 * Checks if comment pagination is included. If not, recommend it.
 */
class Comment_Pagination_Check implements themecheck {
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
			strpos( $php, 'paginate_comments_links' ) === false &&
			strpos( $php, 'the_comments_navigation' ) === false &&
			strpos( $php, 'the_comments_pagination' ) === false &&
			strpos( $php, 'next_comments_link' ) === false &&
			strpos( $php, 'previous_comments_link' ) === false
		) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( "The theme doesn't have comment pagination code in it. Use <strong>paginate_comments_links()</strong> or <strong>the_comments_navigation</strong> or <strong>the_comments_pagination</strong> or <strong>next_comments_link()</strong> and <strong>previous_comments_link()</strong> to add comment pagination.", 'theme-check' )
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

$themechecks[] = new Comment_Pagination_Check();
