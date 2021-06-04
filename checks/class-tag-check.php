<?php
/**
 * Checks if tags are supported
 *
 * @package Theme Check
 */

/**
 * Checks if tags are supported.
 */
class Tag_Check implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		// Combine all the php files into one string to make it easier to search.
		$php = implode( ' ', $php_files );
		checkcount();

		if (
			strpos( $php, 'the_tags' ) === false &&
			strpos( $php, 'the_taxonomies' ) === false &&
			strpos( $php, 'get_the_tag_list' ) === false &&
			strpos( $php, 'get_the_term_list' ) === false
		) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span> %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( "This theme doesn't seem to display tags. Modify it to display tags in appropriate locations.", 'theme-check' )
			);
		}

		return true;
	}

	function getError() {
		return $this->error;
	}
}

$themechecks[] = new Tag_Check();
