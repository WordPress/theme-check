<?php
class Style_Tags implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		checkcount();
		$ret = true;
		$filenames = array();

		foreach( $css_files as $cssfile => $content ) {
			if ( basename( $cssfile ) === 'style.css' ) $data = get_theme_data_from_contents( $content );
		}

		if ( !$data[ 'Tags' ] ) {
			$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: <strong>Tags:</strong> is either empty or missing in style.css header.", "themecheck" );
			return $ret;
		}

		$allowed_tags = array("black","blue","brown","gray","green","orange","pink","purple","red","silver","tan","white","yellow","dark","light","one-column","two-columns","three-columns","four-columns","left-sidebar","right-sidebar","fixed-width","flexible-width","flexible-header", "blavatar","buddypress","custom-background","custom-colors","custom-header","custom-menu","editor-style","featured-image-header","featured-images","front-page-post-form","full-width-template","microformats","post-formats","rtl-language-support","sticky-post","theme-options","threaded-comments","translation-ready","holiday","photoblogging","seasonal");
		
		foreach( $data[ 'Tags' ] as $tag ) {
			if ( !in_array( strtolower( $tag ), $allowed_tags ) ) {
				$this->error[] = sprintf(__('<span class="tc-lead tc-warning">WARNING</span>: Found wrong tag, remove <strong>%1$s</strong> from your style.css header.', 'themecheck'), $tag);
				$ret = false;
			}
		}
		
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Style_Tags;