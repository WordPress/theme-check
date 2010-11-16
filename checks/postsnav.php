<?php

class PostPaginationCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
	
		$ret = true;
		
		// combine all the php files into one string to make it easier to search
		$php = implode(' ', $php_files);
checkcount();
		if ( 
			strpos( $php, 'posts_nav_link' ) === false && 
			strpos( $php, 'paginate_links' ) === false && 
			( strpos( $php, 'previous_posts_link' ) === false &&
			  strpos( $php, 'next_posts_link' ) === false )
		) {
			$this->error[] = "REQUIREDThe theme doesn't have post pagination code in it. Use " . do_strong( 'posts_nav_link()' ) ." or " . do_strong( 'paginate_links()' ) . " or " . do_strong( 'next_posts_link()' ) ." and " . do_strong( 'previous_posts_link()' ) . " to add post pagination.";
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new PostPaginationCheck;
