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
			$this->error[] = '<span class="tc-lead tc-recommended">' . __('RECOMMENDED','theme-check') . '</span>: ' . __( '<strong>Tags:</strong> is either empty or missing in style.css header.', 'theme-check' );
			return $ret;
		}
		$deprecated_tags = array("flexible-width","fixed-width","black","blue","brown","gray","green","orange","pink","purple","red","silver","tan","white","yellow","dark","light","fixed-layout","fluid-layout","responsive-layout","blavatar","holiday","photoblogging","seasonal");
		$allowed_tags = array('grid-layout',"one-column","two-columns","three-columns","four-columns","left-sidebar","right-sidebar","flexible-header",'footer-widgets',"accessibility-ready","buddypress","custom-background","custom-colors","custom-header","custom-menu","custom-logo","editor-style","featured-image-header","featured-images","front-page-post-form","full-width-template","microformats","post-formats","rtl-language-support","sticky-post","theme-options","threaded-comments","translation-ready",'blog','e-commerce','education','entertainment','food-and-drink','holiday','news','photography','portfolio');

		foreach( $data[ 'Tags' ] as $tag ) {

			if ( strpos( strtolower( $tag ), "accessibility-ready") !== false ) {
				$this->error[] = '<span class="tc-lead tc-info">'. __('INFO','theme-check'). '</span>: ' . __( 'Themes that use the tag accessibility-ready will need to undergo an accessibility review.','theme-check' ) . ' ' . __('See <a href="https://make.wordpress.org/themes/handbook/review/accessibility/">https://make.wordpress.org/themes/handbook/review/accessibility/</a>', 'theme-check' );
			}

			if ( ! in_array( strtolower( $tag ), $allowed_tags ) ) {
				if ( in_array( strtolower( $tag ), $deprecated_tags ) ) {
					$this->error[] = '<span class="tc-lead tc-warning">'. __('WARNING','theme-check'). '</span>: ' . sprintf( __('The tag %s has been deprecated, please remove it from your style.css header.', 'theme-check'), '<strong>' . $tag . '</strong>' );
				} else {
					$this->error[] = '<span class="tc-lead tc-warning">'. __('WARNING','theme-check'). '</span>: ' . sprintf( __('Found wrong tag, remove %s from your style.css header.', 'theme-check'), '<strong>' . $tag . '</strong>' );
					$ret = false;
				}
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Style_Tags;
