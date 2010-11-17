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
			$this->error[] = "REQUIREDThe theme doesn't have post pagination code in it. Use <strong>posts_nav_link()</strong> or <strong>paginate_links()</strong> or <strong>next_posts_link()</strong> and <strong>previous_posts_link()</strong> to add post pagination.";
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new PostPaginationCheck;
