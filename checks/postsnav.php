<?php

class PostPaginationCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();
		if ( strpos( $php, 'posts_nav_link' ) === false && 
		     strpos( $php, 'paginate_links' ) === false && 
		     strpos( $php, 'the_posts_pagination' ) === false &&
		     strpos( $php, 'the_posts_navigation' ) === false &&
		   ( strpos( $php, 'previous_posts_link' ) === false && strpos( $php, 'next_posts_link' ) === false )
		   ) {
			$this->error[] = '<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.sprintf( __('The theme doesn\'t have post pagination code in it. Use %1$s or %2$s or %3$s or %4$s or %5$s and %6$s to add post pagination.', 'theme-check' ), '<strong>posts_nav_link()</strong>', '<strong>paginate_links()</strong>', '<strong>the_posts_pagination()</strong>', '<strong>the_posts_navigation()</strong>', '<strong>next_posts_link()</strong>', '<strong>previous_posts_link()</strong>' );
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new PostPaginationCheck;