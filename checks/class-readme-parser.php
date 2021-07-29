<?php
/**
 * Readme Parser.
 *
 * @package Theme Check
 */
class Readme_Parser {

	/**
	 * @var string
	 */
	public $name = '';

	/**
	 * @var array
	 */
	public $tags = array();

	/**
	 * @var string
	 */
	public $requires = '';

	/**
	 * @var string
	 */
	public $tested = '';

	/**
	 * @var string
	 */
	public $requires_php = '';

	/**
	 * @var array
	 */
	public $contributors = array();

	/**
	 * @var string
	 */
	public $donate_link = '';

	/**
	 * @var string
	 */
	public $short_description = '';

	/**
	 * @var string
	 */
	public $license = '';

	/**
	 * @var string
	 */
	public $license_uri = '';

	/**
	 * @var array
	 */
	public $sections = array();

	/**
	 * @var array
	 */
	public $upgrade_notice = array();

	/**
	 * @var array
	 */
	public $faq = array();

	/**
	 * Warning flags which indicate specific parsing failures have occured.
	 *
	 * @var array
	 */
	public $warnings = array();

	/**
	 * These are the readme sections that we expect.
	 *
	 * @var array
	 */
	private $expected_sections = array(
		'description',
		'installation',
		'faq',
		'changelog',
		'upgrade_notice',
		'other_notes',
	);

	/**
	 * We alias these sections, from => to
	 *
	 * @var array
	 */
	private $alias_sections = array(
		'frequently_asked_questions' => 'faq',
		'change_log'                 => 'changelog',
	);

	/**
	 * These are the valid header mappings for the header.
	 *
	 * @var array
	 */
	private $valid_headers = array(
		'tested'            => 'tested',
		'tested up to'      => 'tested',
		'requires'          => 'requires',
		'requires at least' => 'requires',
		'requires php'      => 'requires_php',
		'tags'              => 'tags',
		'contributors'      => 'contributors',
		'donate link'       => 'donate_link',
		'license'           => 'license',
		'license uri'       => 'license_uri',
	);

	/**
	 * Parser constructor.
	 *
	 * @param string $string Contents of a readme to parse.
	 *
	 */
	public function __construct( $string ) {
		$this->parse_readme_contents( $string );
	}

	/**
	 * @param string $contents The contents of the readme to parse.
	 * @return bool
	 */
	protected function parse_readme_contents( $contents ) {
		if ( preg_match( '!!u', $contents ) ) {
			$contents = preg_split( '!\R!u', $contents );
		} else {
			$contents = preg_split( '!\R!', $contents ); // regex failed due to invalid UTF8 in $contents, see #2298
		}
		$contents = array_map( array( $this, 'strip_newlines' ), $contents );

		// Strip UTF8 BOM if present.
		if ( 0 === strpos( $contents[0], "\xEF\xBB\xBF" ) ) {
			$contents[0] = substr( $contents[0], 3 );
		}

		// Convert UTF-16 files.
		if ( 0 === strpos( $contents[0], "\xFF\xFE" ) ) {
			foreach ( $contents as $i => $line ) {
				$contents[ $i ] = mb_convert_encoding( $line, 'UTF-8', 'UTF-16' );
			}
		}

		$line       = $this->get_first_nonwhitespace( $contents );
		$this->name = $this->sanitize_text( trim( $line, "#= \t\0\x0B" ) );

		// Strip Github style header\n==== underlines.
		if ( ! empty( $contents ) && '' === trim( $contents[0], '=-' ) ) {
			array_shift( $contents );
		}

		// Handle readme's which do `=== Theme Name ===\nMy SuperAwesome Name\n...`
		if ( 'theme name' == strtolower( $this->name ) ) {
			$this->name = $line = $this->get_first_nonwhitespace( $contents );

			// Ensure that the line read wasn't an actual header or description.
			if ( strlen( $line ) > 50 || preg_match( '~^(' . implode( '|', array_keys( $this->valid_headers ) ) . ')\s*:~i', $line ) ) {
				$this->name = false;
				array_unshift( $contents, $line );
			}
		}

		// Parse headers.
		$headers = array();

		$line = $this->get_first_nonwhitespace( $contents );
		do {
			$value = null;
			if ( false === strpos( $line, ':' ) ) {

				// Some themes have line-breaks within the headers.
				if ( empty( $line ) ) {
					break;
				} else {
					continue;
				}
			}

			$bits                = explode( ':', trim( $line ), 2 );
			list( $key, $value ) = $bits;
			$key                 = strtolower( trim( $key, " \t*-\r\n" ) );
			if ( isset( $this->valid_headers[ $key ] ) ) {
				$headers[ $this->valid_headers[ $key ] ] = trim( $value );
			}
		} while ( ( $line = array_shift( $contents ) ) !== null );
		array_unshift( $contents, $line );

		if ( ! empty( $headers['requires'] ) ) {
			$this->requires = $this->sanitize_requires_version( $headers['requires'] );
		}
		if ( ! empty( $headers['tested'] ) ) {
			$this->tested = $this->sanitize_tested_version( $headers['tested'] );
		}
		if ( ! empty( $headers['requires_php'] ) ) {
			$this->requires_php = $this->sanitize_requires_php( $headers['requires_php'] );
		}
		if ( ! empty( $headers['contributors'] ) ) {
			$this->contributors = explode( ',', $headers['contributors'] );
			$this->contributors = array_map( 'trim', $this->contributors );
		}
		if ( ! empty( $headers['license'] ) ) {
			// Handle the many cases of "License: GPLv2 - http://..."
			if ( empty( $headers['license_uri'] ) && preg_match( '!(https?://\S+)!i', $headers['license'], $url ) ) {
				$headers['license_uri'] = $url[1];
				$headers['license']     = trim( str_replace( $url[1], '', $headers['license'] ), " -*\t\n\r\n" );
			}
			$this->license = $headers['license'];
		}
		if ( ! empty( $headers['license_uri'] ) ) {
			$this->license_uri = $headers['license_uri'];
		}

		return true;
	}

	/**
	 * @access protected
	 *
	 * @param string $contents
	 * @return string
	 */
	protected function get_first_nonwhitespace( &$contents ) {
		while ( ( $line = array_shift( $contents ) ) !== null ) {
			$trimmed = trim( $line );
			if ( ! empty( $trimmed ) ) {
				break;
			}
		}

		return $line;
	}

	/**
	 * @access protected
	 *
	 * @param string $line
	 * @return string
	 */
	protected function strip_newlines( $line ) {
		return rtrim( $line, "\r\n" );
	}

	/**
	 * @access protected
	 *
	 * @param string $text
	 * @return string
	 */
	protected function sanitize_text( $text ) {
		// not fancy
		$text = strip_tags( $text );
		$text = esc_html( $text );
		$text = trim( $text );

		return $text;
	}

	/**
	 * Sanitizes the Requires PHP header to ensure that it's a valid version header.
	 *
	 * @param string $version
	 * @return string The sanitized $version
	 */
	protected function sanitize_requires_php( $version ) {
		$version = trim( $version );

		// x.y or x.y.z
		if ( $version && ! preg_match( '!^\d+(\.\d+){1,2}$!', $version ) ) {
			$this->warnings['requires_php_header_ignored'] = true;
			// Ignore the readme value.
			$version = '';
		}

		return $version;
	}

	/**
	 * Sanitizes the Tested header to ensure that it's a valid version header.
	 *
	 * @param string $version
	 * @return string The sanitized $version
	 */
	protected function sanitize_tested_version( $version ) {
		$version = trim( $version );
		$latest_wordpress_version = '5.8';

		if ( $version ) {

			// Handle the edge-case of 'WordPress 5.0' and 'WP 5.0' for historical purposes.
			$strip_phrases = [
				'WordPress',
				'WP',
			];
			$version = trim( str_ireplace( $strip_phrases, '', $version ) );

			// Strip off any -alpha, -RC, -beta suffixes, as these complicate comparisons and are rarely used.
			list( $version, ) = explode( '-', $version );

			if (
				// x.y or x.y.z
				! preg_match( '!^\d+\.\d(\.\d+)?$!', $version ) ||
				// Allow themes to mark themselves as compatible with Stable+0.1 (trunk/master) but not higher
				(
					$latest_wordpress_version &&
					version_compare( (float)$version, (float)$latest_wordpress_version+0.1, '>' )
				)
			 ) {
				$this->warnings['tested_header_ignored'] = true;
				// Ignore the readme value.
				$version = '';
			}
		}

		return $version;
	}

	/**
	 * Sanitizes the Requires at least header to ensure that it's a valid version header.
	 *
	 * @param string $version
	 * @return string The sanitized $version
	 */
	protected function sanitize_requires_version( $version ) {
		$version = trim( $version );
		$latest_wordpress_version = '5.9';
		if ( $version ) {

			// Handle the edge-case of 'WordPress 5.0' and 'WP 5.0' for historical purposes.
			$strip_phrases = [
				'WordPress',
				'WP',
				'or higher',
				'and above',
				'+',
			];
			$version = trim( str_ireplace( $strip_phrases, '', $version ) );
			
			// Strip off any -alpha, -RC, -beta suffixes, as these complicate comparisons and are rarely used.
			list( $version, ) = explode( '-', $version );

			if (
				// x.y or x.y.z
				! preg_match( '!^\d+\.\d(\.\d+)?$!', $version ) ||
				// Allow themes to mark themselves as requireing Stable+0.1 (trunk/master) but not higher
				$latest_wordpress_version && ( (float)$version > (float)$latest_wordpress_version+0.1 )
			 ) {
				$this->warnings['requires_header_ignored'] = true;
				// Ignore the readme value.
				$version = '';
			}
		}

		return $version;
	}

}
