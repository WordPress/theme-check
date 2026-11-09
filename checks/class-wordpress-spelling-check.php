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

			$violation_lines = $this->find_bad_spellings( $file_content, $file_path );
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
	 * @param string $content   File content.
	 * @param string $file_path File path.
	 * @return array Line number => line content for violating lines.
	 */
	protected function find_bad_spellings( $content, $file_path ) {
		$violation_lines    = array();
		$normalized_content = $this->normalize_content_for_check( $content, $file_path );
		$normalized_lines   = preg_split( "/\r\n|\n|\r/", $normalized_content );
		$original_lines     = preg_split( "/\r\n|\n|\r/", $content );

		foreach ( $normalized_lines as $line_index => $line_content ) {
			if ( preg_match( '/\bword\s+press\b/i', $line_content ) || preg_match( '/\bwordpres\b/i', $line_content ) ) {
				$line_number                     = $line_index + 1;
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

				$line_number                     = $line_index + 1;
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
		$line_grep = '';

		foreach ( $violation_lines as $line_number => $line_text ) {
			$line_grep .= "<pre class='tc-grep'>" . __( 'Line ', 'theme-check' ) . $line_number . ': ' . htmlspecialchars( $line_text ) . '</pre>';
		}

		return $line_grep;
	}

	/**
	 * Normalize file content before scanning for misspellings.
	 *
	 * This keeps line structure intact while masking known false-positive regions.
	 *
	 * @param string $content   File content.
	 * @param string $file_path File path.
	 * @return string
	 */
	protected function normalize_content_for_check( $content, $file_path ) {
		if ( preg_match( '/\.php$/i', $file_path ) ) {
			$content = $this->mask_php_machine_identifiers( $content );
		}

		$content = $this->mask_block_comments( $content );
		$content = $this->mask_wordpress_in_html_attributes( $content );

		return $content;
	}

	/**
	 * Ignore literal WordPress slugs used as PHP array keys or values of a
	 * machine-readable "value" key, while continuing to check display strings.
	 *
	 * Tokenization avoids treating translated labels or echoed text as data.
	 * Replacing characters without changing length or line endings preserves
	 * the existing line-number and context reporting.
	 *
	 * @param string $content PHP file content.
	 * @return string
	 */
	protected function mask_php_machine_identifiers( $content ) {
		$tokens = token_get_all( $content );
		$text   = array();
		foreach ( $tokens as $index => $token ) {
			$text[ $index ] = is_array( $token ) ? $token[1] : $token;
		}
		$original_text = $text;

		foreach ( $tokens as $index => $token ) {
			if ( ! is_array( $token ) || T_CONSTANT_ENCAPSED_STRING !== $token[0] ) {
				continue;
			}

			if ( ! in_array( $token[1], array( "'wordpress'", '"wordpress"' ), true ) ) {
				continue;
			}

			$previous = $this->next_significant_php_token( $tokens, $index, -1 );
			$next     = $this->next_significant_php_token( $tokens, $index, 1 );
			$is_key   = null !== $next && T_DOUBLE_ARROW === $tokens[ $next ][0];
			$is_value = false;

			if ( null !== $previous && is_array( $tokens[ $previous ] ) && T_DOUBLE_ARROW === $tokens[ $previous ][0] ) {
				$key_index = $this->next_significant_php_token( $tokens, $previous, -1 );
				$is_value  = null !== $key_index &&
					in_array( $text[ $key_index ], array( "'value'", '"value"' ), true ) &&
					$this->is_value_in_wordpress_entry( $tokens, $original_text, $key_index );
			}

			if ( $is_key || $is_value ) {
				$text[ $index ] = str_repeat( 'x', strlen( $token[1] ) );
			}
		}

		return implode( '', $text );
	}

	/**
	 * Check that a value field belongs to the array keyed by "wordpress".
	 *
	 * @param array $tokens PHP tokens.
	 * @param array $text   Token text.
	 * @param int   $index Index of the "value" key.
	 * @return bool
	 */
	protected function is_value_in_wordpress_entry( $tokens, $text, $index ) {
		$depth = 0;
		for ( --$index; $index >= 0; --$index ) {
			if ( ']' === $text[ $index ] ) {
				++$depth;
			} elseif ( '[' === $text[ $index ] ) {
				if ( $depth > 0 ) {
					--$depth;
					continue;
				}

				$arrow_index = $this->next_significant_php_token( $tokens, $index, -1 );
				$key_index   = null !== $arrow_index ? $this->next_significant_php_token( $tokens, $arrow_index, -1 ) : null;
				return null !== $arrow_index && is_array( $tokens[ $arrow_index ] ) && T_DOUBLE_ARROW === $tokens[ $arrow_index ][0] &&
					null !== $key_index && in_array( $text[ $key_index ], array( "'wordpress'", '"wordpress"' ), true );
			}
		}

		return false;
	}

	/**
	 * Find a neighboring PHP token while skipping whitespace and comments.
	 *
	 * @param array $tokens PHP tokens.
	 * @param int   $index  Starting index.
	 * @param int   $step   Direction (-1 or 1).
	 * @return int|null
	 */
	protected function next_significant_php_token( $tokens, $index, $step ) {
		for ( $index += $step; isset( $tokens[ $index ] ); $index += $step ) {
			$token = $tokens[ $index ];
			if ( is_array( $token ) && in_array( $token[0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) ) {
				continue;
			}
			return $index;
		}

		return null;
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
