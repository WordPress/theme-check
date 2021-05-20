<?php

class BlockPatternsCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		checkcount();
		$ret = true;

		$php = implode( ' ', $php_files );

		if ( strpos( $php, 'register_block_pattern' ) === false ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( 'No reference to <strong>register_block_pattern</strong> was found in the theme. Theme authors are encouraged to implement custom block patterns as a transition to block-based themes.', 'theme-check' )
			);
		}

		if ( strpos( $php, 'register_block_style' ) === false ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s',
				__( 'RECOMMENDED', 'theme-check' ),
				__( 'No reference to <strong>register_block_style</strong> was found in the theme. Theme authors are encouraged to implement new block styles as a transition to block-based themes.', 'theme-check' )
			);
		}

		return $ret;
	}

	function getError() {
		return $this->error;
	}
}
$themechecks[] = new BlockPatternsCheck();
