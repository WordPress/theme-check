<?php
/**
 * Validate readme files.
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
	 * Latest WordPress version
	 *
	 * @var string $latest_wordpress_version
	 */
	protected $latest_wordpress_version = '5.8';

	/**
	 * Check that return true for good/okay/acceptable, false for bad/not-okay/unacceptable.
	 *
	 * @param array $php_files File paths and content for PHP files.
	 * @param array $css_files File paths and content for CSS files.
	 * @param array $other_files Folder names, file paths and content for other files.
	 */
	public function check( $php_files, $css_files, $other_files ) {

		checkcount();

		// Get a list of file names and check for the readme.
		$other_filenames = array();
		foreach ( $other_files as $path => $contents ) {
			$other_filenames[] = tc_filename( $path );
			if ( tc_filename( $path ) == 'readme.txt' || tc_filename( $path ) == 'readme.md' ) {
				$readme = $contents;
				break;
			}
		}

		// Publish an error if there is no readme file.
		if ( ! in_array( 'readme.txt', $other_filenames, true ) && ! in_array( 'readme.md', $other_filenames, true ) ) {
			$this->error[] = sprintf(
				'<span class="tc-lead tc-required">%s</span>: %s',
				__( 'ERROR', 'theme-check' ),
				__( 'The readme file is missing.', 'theme-check' )
			);
			$ret           = false;
		} else {
			// Parse the content of the readme.
			$readme = new Readme_Parser( $readme );

			// Error.
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

			if ( isset( $readme->warnings['contributor_ignored'] ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: %s: theme header tag */
						__( 'One or more contributors listed in the readme were ignored. The %s field should only contain WordPress.org usernames. Remember that usernames are case-sensitive.', 'theme-check' ),
						'<code>Contributors</code>'
					)
				);
			} elseif ( ! count( $readme->contributors ) ) {
				$this->error[] = sprintf(
					'<span class="tc-lead tc-warning">%s</span>: %s',
					__( 'README WARNING', 'theme-check' ),
					sprintf(
						/* translators: %s: theme header tag */
						__( 'The %s field is missing from the readme.', 'theme-check' ),
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
