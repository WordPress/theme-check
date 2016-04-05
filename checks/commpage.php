<?php

class CommentPaginationCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();
		if (strpos( $php, 'paginate_comments_links' ) === false &&
			strpos( $php, 'the_comments_navigation' ) === false &&
			strpos( $php, 'the_comments_pagination' ) === false &&
		    (strpos( $php, 'next_comments_link' ) === false && strpos( $php, 'previous_comments_link' ) === false ) ) {

			$this->error[] = '<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.sprintf( __('The theme doesn\'t have comment pagination code in it. Use %1$s or %2$s or %3$s or %4$s and %5$s to add comment pagination.', 'theme-check' ), '<strong>paginate_comments_links()</strong>', '<strong>the_comments_navigation</strong>', '<strong>the_comments_pagination</strong>', '<strong>next_comments_link()</strong>', '<strong>previous_comments_link()</strong>' );
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CommentPaginationCheck;
