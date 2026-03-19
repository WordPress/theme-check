<?php
/**
 * Check spelling and capitalization of WordPress.
 *
 * @package Theme Check
 */

// phpcs:disable WordPress.WP.CapitalPDangit -- Intentional lowercase/typo variants are match targets in this checker.
/**
 * Check that WordPress is spelled and capitalized correctly in text.
 *
 * This check allows lowercase "wordpress" in URL-like contexts and masks
 * Gutenberg block comments and HTML attribute values to reduce false positives.
 */
class WordPress_Spelling_Check implements themecheck {
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
		$ret = true;

		$files_to_check = array_merge( $php_files, $css_files, $this->filter_other_text_files( $other_files ) );

		foreach ( $files_to_check as $file_path => $file_content ) {
			checkcount();

			$violation_lines = $this->find_bad_spellings( $file_content );
			if ( empty( $violation_lines ) ) {
				continue;
			}

			$filename = tc_filename( $file_path );
			$grep     = $this->build_violation_context( $violation_lines );

			$this->error[] = sprintf(
				'<span class="tc-lead tc-required">%s</span>: %s %s',
				__( 'REQUIRED', 'theme-check' ),
				sprintf(
					__( 'Found incorrect spelling or capitalization of %1$s in the file %2$s. Always use <strong>WordPress</strong> in text.', 'theme-check' ),
					'<strong>WordPress</strong>',
					'<strong>' . $filename . '</strong>'
				),
				$grep
			);

			$ret = false;
		}

		return $ret;
	}

	/**
	 * Get error messages from the checks.
	 *
	 * @return array Error message.
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * Keep only likely text files from the non-php/css list.
	 *
	 * @param array $other_files Folder names, file paths and content for other files.
	 * @return array
	 */
	protected function filter_other_text_files( $other_files ) {
		$text_extensions = '/\.(txt|md|html|htm|json|js|xml|svg|po|pot|php|css)$/i';
		$text_files      = array();

		foreach ( $other_files as $file_path => $file_content ) {
			if ( preg_match( $text_extensions, $file_path ) ) {
				$text_files[ $file_path ] = $file_content;
			}
		}

		return $text_files;
	}

	/**
	 * Find non-compliant spellings of "WordPress" on a per-line basis.
	 *
	 * Detects:
	 * - Incorrect capitalization variants such as "Wordpress" and plain "wordpress" in text.
	 * - Common typos such as "word press" and "wordpres".
	 *
	 * @param string $content File content.
	 * @return array Line number => line content for violating lines.
	 */
	protected function find_bad_spellings( $content ) {
		$violation_lines    = array();
		$normalized_content = $this->normalize_content_for_check( $content );
		$normalized_lines   = preg_split( "/\r\n|\n|\r/", $normalized_content );
		$original_lines     = preg_split( "/\r\n|\n|\r/", $content );

		foreach ( $normalized_lines as $line_index => $line_content ) {
			if ( preg_match( '/\bword\s+press\b/i', $line_content ) || preg_match( '/\bwordpres\b/i', $line_content ) ) {
				$line_number                    = $line_index + 1;
				$violation_lines[ $line_number ] = isset( $original_lines[ $line_index ] ) ? $original_lines[ $line_index ] : '';
				continue;
			}

			if ( ! preg_match_all( '/\bwordpress\b/i', $line_content, $matches, PREG_OFFSET_CAPTURE ) ) {
				continue;
			}

			foreach ( $matches[0] as $match ) {
				$word   = $match[0];
				$offset = $match[1];

				if ( 'WordPress' === $word ) {
					continue;
				}

				if ( 'wordpress' === $word && $this->is_allowed_lowercase_url_context( $line_content, $offset, strlen( $word ) ) ) {
					continue;
				}

				$line_number                    = $line_index + 1;
				$violation_lines[ $line_number ] = isset( $original_lines[ $line_index ] ) ? $original_lines[ $line_index ] : '';
				break;
			}
		}

		return $violation_lines;
	}

	/**
	 * Build grep-like context only for actual violating matches.
	 *
	 * @param array $violation_lines Line number => line text for real violations.
	 * @return string HTML snippet matching existing tc_grep-style output.
	 */
	protected function build_violation_context( $violation_lines ) {
		$line_grep    = '';

		foreach ( $violation_lines as $line_number => $line_text ) {
			$line_grep     .= "<pre class='tc-grep'>" . __( 'Line ', 'theme-check' ) . $line_number . ': ' . htmlspecialchars( $line_text ) . '</pre>';
		}

		return $line_grep;
	}

	/**
	 * Normalize file content before scanning for misspellings.
	 *
	 * This keeps line structure intact while masking known false-positive regions.
	 *
	 * @param string $content File content.
	 * @return string
	 */
	protected function normalize_content_for_check( $content ) {
		$content = $this->mask_block_comments( $content );
		$content = $this->mask_wordpress_in_html_attributes( $content );

		return $content;
	}

	/**
	 * Mask Gutenberg block comments before checking text capitalization.
	 *
	 * Keep original length and line breaks so match offsets still map to real lines.
	 *
	 * @param string $content File content.
	 * @return string
	 */
	protected function mask_block_comments( $content ) {
		return preg_replace_callback(
			'/<!--\s*\/?wp:[\s\S]*?-->/',
			function ( $comment_match ) {
				return preg_replace( '/[^\r\n]/', ' ', $comment_match[0] );
			},
			$content
		);
	}

	/**
	 * Mask any "wordpress" token inside HTML attribute values.
	 *
	 * Attribute values often contain slugs/service identifiers rather than user-facing text.
	 *
	 * @param string $content File content.
	 * @return string
	 */
	protected function mask_wordpress_in_html_attributes( $content ) {
		// Attribute values are machine data in many templates; skip spelling checks there.
		return preg_replace_callback(
			'/<[^>]+>/s',
			function ( $tag_match ) {
				return preg_replace_callback(
					'/=\s*("[^"]*"|\'[^\']*\')/s',
					function ( $attribute_match ) {
						return preg_replace_callback(
							'/\bwordpress\b/i',
							function ( $word_match ) {
								return str_repeat( 'x', strlen( $word_match[0] ) );
							},
							$attribute_match[0]
						);
					},
					$tag_match[0]
				);
			},
			$content
		);
	}

	/**
	 * Determine if lowercase "wordpress" appears in an allowed URL-like context.
	 *
	 * @param string $content The current line content.
	 * @param int    $offset  Offset of the match in the line.
	 * @param int    $length  Length of the match.
	 * @return bool
	 */
	protected function is_allowed_lowercase_url_context( $content, $offset, $length ) {
		$before = '';
		$after  = '';

		if ( $offset > 0 ) {
			$before = $content[ $offset - 1 ];
		}

		if ( ( $offset + $length ) < strlen( $content ) ) {
			$after = $content[ $offset + $length ];
		}

		$allowed_neighbors = array( '/', '.', '-', '_', ':', '?', '#', '&', '=', '%', '@' );

		return in_array( $before, $allowed_neighbors, true ) || in_array( $after, $allowed_neighbors, true );
	}
}
// phpcs:enable WordPress.WP.CapitalPDangit

$themechecks[] = new WordPress_Spelling_Check();
