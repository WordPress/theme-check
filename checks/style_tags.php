<?php
class Style_Tags implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		checkcount();
		$ret = true;
		$filenames = array();

		foreach ( $css_files as $css_key => $content ) {
			array_push( $filenames,  $css_key );
		}

		foreach( $filenames as $cssfile ) {
			if ( basename( $cssfile ) === 'style.css' ) $data = get_theme_data( $cssfile );
		}

		if ( !$data[ 'Tags' ] ) {
			$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: <strong>Tags:</strong> is either empty or missing in style.css header.", "themecheck" );
			$ret = false;
			return $ret;
		}

		$allowed_tags = array( 'black', 'blue', 'brown', 'green', 'orange', 'pink', 'purple', 'red', 'silver', 'tan', 'white', 'yellow', 'dark', 'light', 'one-column', 'two-columns',
			'three-columns', 'four-columns', 'left-sidebar', 'right-sidebar', 'fixed-width', 'flexible-width', 'custom-colors', 'custom-header', 'custom-background',
			'custom-menu', 'editor-style', 'theme-options', 'threaded-comments', 'sticky-post', 'microformats', 'rtl-language-support', 'translation-ready', 'front-page-post-form',
			'buddypress', 'holiday', 'photoblogging', 'seasonal' );

		foreach( $data[ 'Tags' ] as $tag ) {
			if ( !in_array( $tag, $allowed_tags ) ) $this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: Found wrong tag, remove <strong>{$tag}</strong> from your style.css header.";
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Style_Tags;