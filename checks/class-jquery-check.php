<?php
/**
 * Checks if jQuery is loaded by the theme.
 *
 * @package Theme Check
 */

/**
 * Checks if jQuery is loaded by the theme.
 *
 * Themes load jQuery both by enqueueing it directly and by listing it as a dependency
 * of one of their own scripts, so both cases are reported.
 */
class JQuery_Check implements themecheck {
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
		checkcount();

		$calls = $this->get_script_calls( $php_files );

		// Dependencies of every script the theme registers or enqueues, keyed by handle.
		$dependencies = array();
		foreach ( $calls as $call ) {
			if ( '' !== $call['handle'] ) {
				if ( ! isset( $dependencies[ $call['handle'] ] ) ) {
					$dependencies[ $call['handle'] ] = array();
				}
				$dependencies[ $call['handle'] ] = array_merge( $dependencies[ $call['handle'] ], $call['deps'] );
			}
		}

		// Registering a script does not load it, so only enqueued scripts are followed.
		$enqueued = array();
		foreach ( $calls as $call ) {
			if ( 'enqueue' === $call['type'] ) {
				$enqueued = array_merge( $enqueued, array( $call['handle'] ), $call['deps'] );
			}
		}
		$loaded = $this->resolve_handles( $enqueued, $dependencies );

		$lines = array();
		foreach ( $calls as $call ) {
			if ( 'register' === $call['type'] && ! isset( $loaded[ $call['handle'] ] ) ) {
				continue;
			}

			$handles = $this->resolve_handles( array_merge( array( $call['handle'] ), $call['deps'] ), $dependencies );
			if ( ! preg_grep( '/^jquery(?:-core|-migrate|-ui-[a-z0-9_-]+)?$/', array_keys( $handles ) ) ) {
				continue;
			}

			$lines[ $call['file'] ][ $call['line'] ] = "<pre class='tc-grep'>" . __( 'Line ', 'theme-check' ) . $call['line'] . ': ' . htmlspecialchars( $call['text'] ) . '</pre>';
		}

		foreach ( $lines as $file_path => $file_lines ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s %s',
				__( 'RECOMMENDED', 'theme-check' ),
				sprintf(
					/* translators: 1: file name, 2: URL of an article about the performance impact of jQuery. */
					__( 'jQuery is loaded by %1$s, either enqueued directly or listed as a dependency of another script. You may not need it, see <a href="%2$s">this article</a> for tips on switching to vanilla JavaScript.', 'theme-check' ),
					'<strong>' . tc_filename( $file_path ) . '</strong>',
					'https://make.wordpress.org/themes/2021/10/04/the-performance-impact-of-using-jquery-in-wordpress-themes/'
				),
				implode( '', $file_lines )
			);
		}

		return true;
	}

	/**
	 * Finds the wp_enqueue_script() and wp_register_script() calls in the theme.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @return array[] {
	 *     One entry per call.
	 *
	 *     @type string   $type   Either 'enqueue' or 'register'.
	 *     @type string   $handle The script handle, or an empty string when it is not a string literal.
	 *     @type string[] $deps   String literals found in the arrays passed to the call.
	 *     @type string   $file   Path of the file containing the call.
	 *     @type int      $line   Line the call starts on.
	 *     @type string   $text   The call, on a single line.
	 * }
	 */
	protected function get_script_calls( $php_files ) {
		$calls = array();

		foreach ( $php_files as $file_path => $file_content ) {
			// The second group matches the argument list, up to the parenthesis closing the call.
			if ( ! preg_match_all( '/\bwp_(enqueue|register)_script\s*(\((?:[^()]++|(?2))*+\))/i', $file_content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
				continue;
			}

			foreach ( $matches as $match ) {
				$arguments = $match[2][0];

				$handle = '';
				if ( preg_match( '/^\(\s*([\'"])([^\'"]+)\1\s*[,)]/', $arguments, $handle_match ) ) {
					$handle = $handle_match[2];
				}

				// The lookbehind skips array access such as $asset['dependencies'].
				$deps = array();
				if ( preg_match_all( '/\barray\s*\(([^()]*)\)|(?<![\w\]\)])\[([^\[\]]*)\]/i', $arguments, $array_matches ) ) {
					preg_match_all( '/([\'"])([^\'"]+)\1/', implode( ',', $array_matches[1] ) . ',' . implode( ',', $array_matches[2] ), $dep_matches );
					$deps = $dep_matches[2];
				}

				$calls[] = array(
					'type'   => strtolower( $match[1][0] ),
					'handle' => $handle,
					'deps'   => $deps,
					'file'   => $file_path,
					'line'   => substr_count( $file_content, "\n", 0, $match[0][1] ) + 1,
					'text'   => preg_replace( '/\s+/', ' ', $match[0][0] ),
				);
			}
		}

		return $calls;
	}

	/**
	 * Resolves script handles to the full set of handles they load.
	 *
	 * @param string[] $handles      Script handles.
	 * @param array[]  $dependencies Dependencies of the theme's scripts, keyed by handle.
	 * @return true[] The handles and all of their dependencies, as array keys.
	 */
	protected function resolve_handles( $handles, $dependencies ) {
		$resolved = array();

		while ( $handles ) {
			$handle = array_pop( $handles );
			if ( '' === $handle || isset( $resolved[ $handle ] ) ) {
				continue;
			}

			$resolved[ $handle ] = true;
			if ( isset( $dependencies[ $handle ] ) ) {
				$handles = array_merge( $handles, $dependencies[ $handle ] );
			}
		}

		return $resolved;
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

$themechecks[] = new JQuery_Check();
