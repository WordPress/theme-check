<?php
/**
 * Checks that Full-Site Editing theme templates have 1 and only 1 h1 tag.
 *
 * @package Theme Check
 */

/**
 * Class FSE_Templates_H1_Check
 *
 * Checks that Full-Site Editing theme templates have 1 and only 1 h1 tag.
 *
 * This check is not added to the global array of checks, because it doesn't apply to all themes.
 */
class FSE_Templates_H1_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

		$ret                             = true;
		$templates                       = get_block_templates();
		$templates_with_multiple_h1_tags = array();
		$templates_with_no_h1_tags       = array();

		foreach ( $templates as $template ) {
			$blocks   = parse_blocks( $template->content );
			$h1_count = $this->count_h1_tags_recursively( $blocks );

			if ( $h1_count > 1 ) {
				$templates_with_multiple_h1_tags[] = $template->slug;
			}

			if ( $h1_count === 0 ) {
				$templates_with_no_h1_tags[] = $template->slug;
			}
		}

		if ( ! empty( $templates_with_multiple_h1_tags ) ) {
			$this->error[] = sprintf( '<span class="tc-lead tc-warning">' . __( 'WARNING', 'theme-check' ) . '</span>: ' . __( 'The following templates have multiple h1 tags: %s', 'theme-check' ), '<strong>' . implode( ', ', $templates_with_multiple_h1_tags ) . '</strong>' );
		}

		if ( ! empty( $templates_with_no_h1_tags ) ) {
			$this->error[] = sprintf( '<span class="tc-lead tc-warning">' . __( 'WARNING', 'theme-check' ) . '</span>: ' . __( 'The following templates have no h1 tags: %s', 'theme-check' ), '<strong>' . implode( ', ', $templates_with_no_h1_tags ) . '</strong>' );
		}

		return $ret;
	}

	/**
	 * Recursively count h1 tags in blocks, including nested blocks.
	 *
	 * @param array $blocks Array of blocks to process.
	 * @return int Number of h1 tags found.
	 */
	private function count_h1_tags_recursively( $blocks ) {
		$h1_count = 0;
		foreach ( $blocks as $block ) {
			if ( ! empty( $block['innerBlocks'] ) ) {
				$h1_count += $this->count_h1_tags_recursively( $block['innerBlocks'] );
			} else {
				if ( $block['blockName'] === 'core/heading' || $block['blockName'] === 'core/post-title' ) {
					if ( isset( $block['attrs']['level'] ) && $block['attrs']['level'] === 1 ) {
						$h1_count++;
					}
				}
			}
		}
		return $h1_count;
	}

	/**
	 * Get error messages from the checks.
	 *
	 * @return array Error message.
	 */
	public function getError() {
		return $this->error;
	}
}
