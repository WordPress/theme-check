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
			$this->error[] = '<span class="tc-lead tc-recommended">' . __('RECOMMENDED','theme-check') . '</span>: ' . sprintf( __( '%s is either empty or missing in style.css header.', 'theme-check' ), '<strong>Tags:</strong>' );
			return $ret;
		}

		$allowed_tags = array("black","blue","brown","gray","green","orange","pink","purple","red","silver","tan","white","yellow","dark","light","one-column","two-columns","three-columns","four-columns","left-sidebar","right-sidebar","fixed-layout","fluid-layout","responsive-layout","flexible-header","accessibility-ready","blavatar","buddypress","custom-background","custom-colors","custom-header","custom-menu","editor-style","featured-image-header","featured-images","front-page-post-form","full-width-template","microformats","post-formats","rtl-language-support","sticky-post","theme-options","threaded-comments","translation-ready","holiday","photoblogging","seasonal");

		foreach( $data[ 'Tags' ] as $tag ) {

			if ( strpos( strtolower( $tag ), "accessibility-ready") !== false ) {
				$this->error[] = '<span class="tc-lead tc-info">'. __('INFO','theme-check'). '</span>: ' . __( 'Themes that use the tag accessibility-ready will need to undergo an accessibility review.','theme-check' ) . ' ' . sprintf( __('See: %s', 'theme-check' ) , '<a href="https://make.wordpress.org/themes/handbook/review/accessibility/">https://make.wordpress.org/themes/handbook/review/accessibility/</a>' );
			}

			if ( !in_array( strtolower( $tag ), $allowed_tags ) ) {
				if ( in_array( strtolower( $tag ), array("flexible-width","fixed-width") ) ) {
					$this->error[] = '<span class="tc-lead tc-warning">'. __('WARNING','theme-check'). '</span>: ' . sprintf( __( 'The %1$s and %2$s tags changed to %3$s and %4$s tags in WordPress 3.8. Additionally, the %5$s tag was added. Please change to using one of the new tags.', 'theme-check' ), '<strong>flexible-width</strong>', '<strong>fixed-width</strong>', '<strong>fluid-layout</strong>', '<strong>fixed-layout</strong>', '<strong>responsive-layout</strong>' );
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