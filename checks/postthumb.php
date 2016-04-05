<?php

class PostThumbnailCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();

		if ( strpos( $php, 'the_post_thumbnail' ) === false ) {
			$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.sprintf( __( 'No reference to %s was found in the theme.', 'theme-check'), '<strong>the_post_thumbnail()</strong>' ).' '.__( 'It is recommended that the theme implement this functionality instead of using custom fields for thumbnails.', 'theme-check' );
		}

		if ( strpos( $php, 'post-thumbnails' ) === false ) {
			$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.sprintf( __( 'No reference to %s was found in the theme.', 'theme-check'), '<strong>post-thumbnails</strong>' ).' '.sprintf( __( 'If the theme has a thumbnail like functionality, it should be implemented with %s in the functions.php file.', 'theme-check' ), '<strong>add_theme_support( "post-thumbnails" )</strong>' );
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new PostThumbnailCheck;