<?php
/**
 * Check if translation functions have a text domain.
 *
 * @package Theme Check
 */

/**
 * Check if translation functions have a text domain.
 */
class TextDomain_Check implements themecheck {
	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Theme name
	 *
	 * @var string $name
	 */
	protected $name = '';

	/**
	 * Theme slug
	 *
	 * @var string $slug
	 */
	protected $slug = '';

	protected $is_wporg = false;

	// Rules come from WordPress core tool makepot.php, modified to have domain info.
	var $rules = array(
		'__'                   => array( 'string', 'domain' ),
		'_e'                   => array( 'string', 'domain' ),
		'_c'                   => array( 'string', 'domain' ),
		'_n'                   => array( 'singular', 'plural', 'domain' ),
		'_n_noop'              => array( 'singular', 'plural', 'domain' ),
		'_nc'                  => array( 'singular', 'plural', 'domain' ),
		'__ngettext'           => array( 'singular', 'plural', 'domain' ),
		'__ngettext_noop'      => array( 'singular', 'plural', 'domain' ),
		'_x'                   => array( 'string', 'context', 'domain' ),
		'_ex'                  => array( 'string', 'context', 'domain' ),
		'_nx'                  => array( 'singular', 'plural', 'context', 'domain' ),
		'_nx_noop'             => array( 'singular', 'plural', 'context', 'domain' ),
		'_n_js'                => array( 'singular', 'plural', 'domain' ),
		'_nx_js'               => array( 'singular', 'plural', 'context', 'domain' ),
		'esc_attr__'           => array( 'string', 'domain' ),
		'esc_html__'           => array( 'string', 'domain' ),
		'esc_attr_e'           => array( 'string', 'domain' ),
		'esc_html_e'           => array( 'string', 'domain' ),
		'esc_attr_x'           => array( 'string', 'context', 'domain' ),
		'esc_html_x'           => array( 'string', 'context', 'domain' ),
		'comments_number_link' => array( 'string', 'singular', 'plural', 'domain' ),
	);

	// Core names their themes differently.
	var $exceptions = array( 'twentyten', 'twentyeleven', 'twentytwelve', 'twentythirteen', 'twentyfourteen', 'twentyfifteen', 'twentysixteen', 'twentyseventeen', 'twentyeighteen', 'twentynineteen', 'twentytwenty', 'twentytwentyone' );

	function set_context( $data ) {
		if ( isset( $data['theme']['Name'] ) ) {
			$this->name = $data['theme']['Name'];
		}
		if ( isset( $data['slug'] ) ) {
			$this->slug = $data['slug'];
		}

		$this->is_wporg = ! empty( $data['is_wporg'] );
	}

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	function check( $php_files, $css_files, $other_files ) {
		$ret   = true;
		$error = '';
		checkcount();

		// Make sure the tokenizer is available.
		if ( ! function_exists( 'token_get_all' ) ) {
			return true;
		}

		$funcs   = array_keys( $this->rules );
		$domains = array();

		foreach ( $php_files as $php_key => $phpfile ) {
			$error = '';
			// Tokenize the file.
			$tokens         = token_get_all( $phpfile );
			$in_func        = false;
			$args_started   = false;
			$parens_balance = 0;
			$found_domain   = false;

			foreach ( $tokens as $token ) {
				$string_success = false;
				if ( is_array( $token ) ) {
					list( $id, $text ) = $token;
					if ( T_STRING == $id && in_array( $text, $funcs ) ) {
						$in_func        = true;
						$func           = $text;
						$parens_balance = 0;
						$args_started   = false;
						$found_domain   = false;
					} elseif ( T_CONSTANT_ENCAPSED_STRING == $id ) {
						if ( $in_func && $args_started ) {
							if ( ! isset( $this->rules[ $func ][ $args_count ] ) ) {
								$filename = tc_filename( $php_key );
								// Avoid a warning when too many arguments are in a function, cause a fail case.
								$new_args   = $args;
								$new_args[] = $text;
								$error      = $new_args['0'];
								$grep       = tc_grep( $error, $php_key );
								$lines      = explode( 'Line', $grep );

								foreach ( $lines as $line ) {
									if ( strpos( $line, $func ) !== false && strpos( $line, $error ) !== false ) {
										$grep = "<pre class='tc-grep'>" . __( 'Line ', 'theme-check' ) . $line . '</pre>';
									}
								}

								$this->error[] = sprintf(
									'<span class="tc-lead tc-warning">%s</span>: %s',
									__( 'WARNING', 'theme-check' ),
									sprintf(
										__( 'Found a translation function that has an incorrect number of arguments in the file %1$s. Function %2$s, with the arguments %3$s. %4$s', 'theme-check' ),
										$filename,
										'<strong>' . $func . '</strong>',
										'<strong>' . implode( ', ', $new_args ) . '</strong>',
										$grep
									)
								);
							} elseif ( $this->rules[ $func ][ $args_count ] == 'domain' ) {
								// Strip quotes from the domain, avoids 'domain' and "domain" not being recognized as the same
								$text         = str_replace( array( '"', "'" ), '', $text );
								$domains[]    = $text;
								$found_domain = true;
							}
							if ( $parens_balance == 1 ) {
								$args_count++;
								$args[] = $text;
							}
						}
					}
					$token = $text;
				} elseif ( '(' == $token ) {
					if ( $parens_balance == 0 ) {
						$args         = array();
						$args_started = true;
						$args_count   = 0;
					}
					++$parens_balance;
				} elseif ( ')' == $token ) {
					--$parens_balance;
					if ( $in_func && 0 == $parens_balance ) {
						$error = implode( ', ', $args );

						if ( ! $found_domain && ! empty( $error ) ) {
							$filename = tc_filename( $php_key );
							$grep     = tc_grep( $error, $php_key );
							$lines    = explode( 'Line', $grep );
							foreach ( $lines as $line ) {
								if ( strpos( $line, $func ) !== false && strpos( $line, $error ) !== false ) {
									$grep = "<pre class='tc-grep'>" . __( 'Line ', 'theme-check' ) . $line . '</pre>';
								}
							}

							$this->error[] = sprintf(
								'<span class="tc-lead tc-warning">%s</span>: %s',
								__( 'WARNING', 'theme-check' ),
								sprintf(
									__( 'Found a translation function that is missing a text-domain in the file %1$s. Function %2$s, with the arguments %3$s. %4$s', 'theme-check' ),
									$filename,
									'<strong>' . $func . '</strong>',
									'<strong>' . $error . '</strong>',
									$grep
								)
							);
						}

						$in_func      = false;
						$func         = '';
						$args_started = false;
						$found_domain = false;
					}
				}
			}
		}

		$domains      = array_unique( $domains );
		$domainlist   = implode( ', ', $domains );
		$domainscount = count( $domains );

		// ignore core themes and uploads on w.org for this one check
		if ( ! in_array( $this->slug, $this->exceptions ) && ! $this->is_wporg ) {
			$correct_domain = sanitize_title_with_dashes( $this->name );
			if ( $this->slug != $correct_domain ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s %s',
					__( 'WARNING', 'theme-check' ),
					sprintf(
						__( "Your theme appears to be in the wrong directory for the theme name. The directory name must match the slug of the theme. This theme's correct slug and text-domain is %s.", 'theme-check' ),
						'<strong>' . $correct_domain . '</strong>'
					),
					__( '(If this is a child theme, you can ignore this error.)', 'theme-check' )
				);
			} elseif ( ! empty( $domains ) && ! in_array( $correct_domain, $domains ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s %s',
					__( 'REQUIRED', 'theme-check' ),
					sprintf(
						__( "This theme text domain does not match the theme's slug. The text domain used: %s", 'theme-check' ),
						'<strong>' . $domainlist . '</strong>'
					),
					sprintf(
						__( "This theme's correct slug and text-domain is %s.", 'theme-check' ),
						'<strong>' . $correct_domain . '</strong>'
					)
				);
				$ret           = false;
			}
		}

		if ( $domainscount > 1 ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-warning">%s</span>: %s %s',
				__( 'WARNING', 'theme-check' ),
				__( 'More than one text-domain is being used in this theme. This means the theme will not be compatible with WordPress.org language packs.', 'theme-check' ),
				sprintf(
					__( 'The domains found are %s.', 'theme-check' ),
					'<strong>' . $domainlist . '</strong>'
				)
			);
		} else {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-info">%s</span>: %s %s',
				__( 'INFO', 'theme-check' ),
				__( "Only one text-domain is being used in this theme. Make sure it matches the theme's slug correctly so that the theme will be compatible with WordPress.org language packs.", 'theme-check' ),
				sprintf(
					__( 'The domain found is %s.', 'theme-check' ),
					'<strong>' . $domainlist . '</strong>'
				)
			);

		}

		if ( $domainscount > 2 ) {
			$ret = false;
		}

		return $ret;
	}

	function getError() {
		return $this->error;
	}
}
$themechecks[] = new TextDomain_Check();
