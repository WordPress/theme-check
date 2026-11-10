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
	 * Matches the script handles registered by WordPress that are jQuery or depend on it.
	 *
	 * @var string
	 */
	const JQUERY_HANDLE_REGEX = '/^jquery(?:-(?:core|migrate|ui-[a-z]+|effects-[a-z]+|color|form|hotkeys|masonry|query|serialize-object|table-hotkeys|touch-punch))?$/';

	/**
	 * Hooks that only fire in the admin, the Customizer, the block editor or on the login screen.
	 *
	 * @var string[]
	 */
	const ADMIN_HOOKS = array(
		'admin_enqueue_scripts',
		'customize_controls_enqueue_scripts',
		'customize_preview_init',
		'enqueue_block_editor_assets',
		'login_enqueue_scripts',
	);

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

		$calls = $this->get_script_calls( $php_files, $this->get_admin_callbacks( $php_files ) );

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
			if ( ! preg_grep( self::JQUERY_HANDLE_REGEX, array_keys( $handles ) ) ) {
				continue;
			}

			$lines[ $call['file'] ][ $call['line'] ] = "<pre class='tc-grep'>" . __( 'Line ', 'theme-check' ) . $call['line'] . ': ' . htmlspecialchars( $call['text'] ) . '</pre>';
		}

		if ( $lines ) {
			$grep = '';
			foreach ( $lines as $file_path => $file_lines ) {
				$grep .= '<strong>' . tc_filename( $file_path ) . '</strong>' . implode( '', $file_lines );
			}

			$this->error[] = sprintf(
				'<span class="tc-lead tc-recommended">%s</span>: %s %s',
				__( 'RECOMMENDED', 'theme-check' ),
				sprintf(
					/* translators: %s: URL of an article about the performance impact of jQuery. */
					__( 'This theme loads jQuery. Many themes no longer need it. See <a href="%s">this article</a> for tips on switching to vanilla JavaScript.', 'theme-check' ),
					'https://make.wordpress.org/themes/2021/10/04/the-performance-impact-of-using-jquery-in-wordpress-themes/'
				),
				$grep
			);
		}

		return true;
	}

	/**
	 * Finds the wp_enqueue_script() and wp_register_script() calls in the theme.
	 *
	 * Calls that only run outside the front end are left out.
	 *
	 * @param array    $php_files       File paths and content for PHP files.
	 * @param string[] $admin_callbacks Names of the functions and methods hooked only to admin hooks.
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
	protected function get_script_calls( $php_files, $admin_callbacks ) {
		$calls = array();

		foreach ( $php_files as $file_path => $file_content ) {
			// The second group matches the argument list, up to the parenthesis closing the call.
			if ( ! preg_match_all( '/\bwp_(enqueue|register)_script\s*(\((?:[^()]++|(?2))*+\))/i', $file_content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
				continue;
			}

			preg_match_all( '/\bfunction\b\s*&?\s*(\w*)\s*\(/i', $file_content, $functions, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );

			foreach ( $matches as $match ) {
				if ( $this->is_admin_call( $file_content, $match[0][1], $functions, $admin_callbacks ) ) {
					continue;
				}

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
	 * Finds the functions and methods that are hooked to admin hooks and to nothing else.
	 *
	 * Methods are matched by name alone, so a name that is also hooked to a front end
	 * action anywhere in the theme is not treated as admin only.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @return string[] Function and method names.
	 */
	protected function get_admin_callbacks( $php_files ) {
		$hooks = array();

		// The callback is a function name, a static method string or an array naming a method.
		$regex = '/\badd_(?:action|filter)\s*\(\s*([\'"])([^\'"]+)\1\s*,\s*(?:(?:array\s*\(|\[)\s*[^,()\[\]]+,\s*)?([\'"])(?:[\w\\\\]+::)?(\w+)\3/i';

		foreach ( $php_files as $file_content ) {
			if ( preg_match_all( $regex, $file_content, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$hooks[ strtolower( $match[4] ) ][] = $match[2];
				}
			}
		}

		$admin_callbacks = array();
		foreach ( $hooks as $callback => $callback_hooks ) {
			if ( ! array_diff( $callback_hooks, self::ADMIN_HOOKS ) ) {
				$admin_callbacks[] = $callback;
			}
		}

		return $admin_callbacks;
	}

	/**
	 * Checks whether a call sits inside a function that only runs on an admin hook.
	 *
	 * @param string   $file_content    Content of the file containing the call.
	 * @param int      $offset          Position of the call in the file.
	 * @param array[]  $functions       Function declarations in the file, as offset-capturing matches.
	 * @param string[] $admin_callbacks Names of the functions and methods hooked only to admin hooks.
	 * @return bool
	 */
	protected function is_admin_call( $file_content, $offset, $functions, $admin_callbacks ) {
		$closure_regex = '/\badd_(?:action|filter)\s*\(\s*([\'"])(?:' . implode( '|', self::ADMIN_HOOKS ) . ')\1\s*,\s*(?:static\s+)?$/i';

		foreach ( $functions as $function ) {
			$start = $function[0][1];
			if ( $start > $offset ) {
				break;
			}

			// Skip functions whose body, the first balanced pair of braces, ends before the call.
			if ( ! preg_match( '/\G[^{;]*(\{(?:[^{}]++|(?1))*+\})/', $file_content, $body, 0, $start + strlen( $function[0][0] ) ) || $start + strlen( $function[0][0] ) + strlen( $body[0] ) < $offset ) {
				continue;
			}

			if ( '' !== $function[1][0] ) {
				if ( in_array( strtolower( $function[1][0] ), $admin_callbacks, true ) ) {
					return true;
				}
			} elseif ( preg_match( $closure_regex, substr( $file_content, 0, $start ) ) ) {
				return true;
			}
		}

		return false;
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
