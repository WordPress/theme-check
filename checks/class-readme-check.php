<?php
/**
 * Validate readme files.
 *
 * @link https://meta.svn.wordpress.org/sites/trunk/wordpress.org/public_html/wp-content/plugins/plugin-directory/readme/
 *
 * @package Theme Check
 */

class Readme_Check implements themecheck {

	/**
	 * Error messages, warnings and info notices.
	 *
	 * @var array $error
	 */
	protected $error = array();

	/**
	 * Theme information. Author URI, theme URI, Author name
	 *
	 * @var object $theme
	 */
	protected $theme;

	/**
	 * Theme slug.
	 *
	 * @var object $slug
	 */
	protected $slug;

	function set_context( $data ) {
		if ( isset( $data['theme'] ) ) {
			$this->theme = $data['theme'];
		}
		if ( isset( $data['slug'] ) ) {
			$this->slug = $data['slug'];
		}
	}

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

		/**
		 * Latest WordPress version
		 *
		 * @var string $latest_wordpress_version
		 */
		if ( defined( 'WP_CORE_LATEST_RELEASE' ) ) {
			// When running on WordPress.org, this constant defines the latest WordPress release.
			$latest_wordpress_version = WP_CORE_LATEST_RELEASE;
		} else {
			// Assume that the local environment being tested in is up to date.
			$latest_wordpress_version = $GLOBALS['wp_version'];
		}

		checkcount();

		// Get a list of file names and check for the readme.
		$readme = '';

		// Get the contents of themeslug/filename:
		foreach ( $other_files as $path => $contents ) {
			if ( stripos( $path, $this->slug . '/readme.txt' ) || stripos( $path, $this->slug . '/readme.md' ) !== false ) {
				$readme .= $contents;
			}
		}

		// Publish an error if there is no readme file.
		if ( empty( $readme ) ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-required">%s</span>: %s',
				__( 'REQUIRED', 'theme-check' ),
				__( 'The readme file is missing.', 'theme-check' )
			);
			$ret           = false;
		} else {
			// Parse the content of the readme.
			$readme = new Readme_Parser( $readme );

			// Error if the theme name is missing in the readme.
			if ( empty( $readme->name ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'README ERROR', 'theme-check' ),
					/* translators: 1: 'Theme Name' section title, 2: 'Theme Name' */
					sprintf(
						__( 'Could not find a theme name in the readme. Theme name format looks like: %1$s. Please change %2$s to reflect the actual name of your theme.', 'theme-check' ),
						'<code>=== Theme Name ===</code>',
						'<code>Theme Name</code>'
					)
				);
				$ret = false;
			} elseif ( $readme->name != $this->theme ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-required">%s</span>: %s',
					__( 'README ERROR', 'theme-check' ),
					/* translators: 1: actual theme name, 2: theme name in readme, 3: 'Theme Name' section title, 4: 'Theme Name' */
					sprintf(
						__( 'The theme name in the readme %1$s does not match the name of your theme %2$s. Theme name format looks like: %3$s. Please change %4$s to reflect the actual name of your theme.', 'theme-check' ),
						'<code>' . esc_html( $readme->name ) . '</code>',
						'<code>' . esc_html( $this->theme ) . '</code>',
						'<code>=== Theme Name ===</code>',
						'<code>Theme Name</code>'
					)
				);
				$ret = false;
			}

			// Warnings.
			if ( isset( $readme->warnings['requires_header_ignored'] ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					/* translators: 1: theme header tag; 2: Example version 5.0. 3: Example version 4.9. */
					sprintf(
						__( 'The %1$s field in the readme was ignored. This field should only contain a valid WordPress version such as %2$s or %3$s.', 'theme-check' ),
						'<code>Requires at least</code>',
						'<code>' . number_format( $latest_wordpress_version, 1 ) . '</code>',
						'<code>' . number_format( $latest_wordpress_version - 0.1, 1 ) . '</code>'
					)
				);
			} elseif ( empty( $readme->requires ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: %s: theme header tag */
						__( 'The %s field is missing from the readme.', 'theme-check' ),
						'<code>Requires at least</code>'
					)
				);
			}

			if ( isset( $readme->warnings['tested_header_ignored'] ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: 1: theme header tag; 2: Example version 5.0. 3: Example version 5.1. */
						__( 'The %1$s field in the readme was ignored. This field should only contain a valid WordPress version such as %2$s or %3$s.', 'theme-check' ),
						'<code>Tested up to</code>',
						'<code>' . number_format( $latest_wordpress_version, 1 ) . '</code>',
						'<code>' . number_format( $latest_wordpress_version + 0.1, 1 ) . '</code>'
					)
				);
			} elseif ( empty( $readme->tested ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: %s: plugin header tag */
						__( 'The %s field is missing from the readme.', 'theme-check' ),
						'<code>Tested up to</code>'
					)
				);
			}

			if ( isset( $readme->warnings['requires_php_header_ignored'] ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: 1: plugin header tag; 2: Example version 5.2.4. 3: Example version 7.0. */
						__( 'The %1$s field in the readme was ignored. This field should only contain a PHP version such as %2$s or %3$s.', 'theme-check' ),
						'<code>Requires PHP</code>',
						'<code>5.2.4</code>',
						'<code>7.0</code>'
					)
				);
			} elseif ( empty( $readme->requires_php ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: %s: plugin header tag */
						__( 'The %s field is missing from the readme.', 'theme-check' ),
						'<code>Requires PHP</code>'
					)
				);
			}

			if ( 2 <= count( $readme->contributors ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: %s: theme header tag */
						__( 'The %s field should only contain one WordPress.org username. Remember that usernames are case-sensitive.', 'theme-check' ),
						'<code>Contributors</code>'
					)
				);
			} elseif ( ! count( $readme->contributors ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: %s: theme header tag */
						__( 'The %s field is missing from the readme or is empty.', 'theme-check' ),
						'<code>Contributors</code>'
					)
				);
			}

			if ( empty( $readme->license ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: %s: theme header tag */
						__( 'The %s field is missing from the readme.', 'theme-check' ),
						'<code>License</code>'
					)
				);
			}

			if ( empty( $readme->license_uri ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: %s: theme header tag */
						__( 'The %s field is missing from the readme.', 'theme-check' ),
						'<code>License URI</code>'
					)
				);
			}

			// Info.
			if ( empty( $readme->sections['description'] ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-info ">%s</span>: %s',
					__( 'README INFO', 'theme-check' ),
					sprintf(
						/* translators: %s: section title */
						__( 'No %s section was found in the readme.', 'theme-check' ),
						'<code>== Description ==</code>'
					)
				);
			}

			if ( empty( $readme->sections['faq'] ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-info ">%s</span>: %s',
					__( 'README INFO', 'theme-check' ),
					sprintf(
						/* translators: %s: section title */
						__( 'No %s section was found in the readme.', 'theme-check' ),
						'<code>== Frequently Asked Questions ==</code>'
					)
				);
			}

			if ( empty( $readme->sections['changelog'] ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-info ">%s</span>: %s',
					__( 'README INFO', 'theme-check' ),
					sprintf(
						/* translators: %s: section title */
						__( 'No %s section was found in the readme.', 'theme-check' ),
						'<code>== Changelog ==</code>'
					)
				);
			}
		}
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

$themechecks[] = new Readme_Check();
