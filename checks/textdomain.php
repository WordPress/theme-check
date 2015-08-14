<?php
class TextDomainCheck implements themecheck {
	protected $error = array();

	// rules come from WordPress core tool makepot.php, modified by me to have domain info
	var $rules = array(
		'_' => array('string', 'domain'),
		'__' => array('string', 'domain'),
		'_e' => array('string', 'domain'),
		'_c' => array('string', 'domain'),
		'_n' => array('singular', 'plural', 'domain'),
		'_n_noop' => array('singular', 'plural', 'domain'),
		'_nc' => array('singular', 'plural', 'domain'),
		'__ngettext' => array('singular', 'plural', 'domain'),
		'__ngettext_noop' => array('singular', 'plural', 'domain'),
		'_x' => array('string', 'context', 'domain'),
		'_ex' => array('string', 'context', 'domain'),
		'_nx' => array('singular', 'plural', 'context', 'domain'),
		'_nx_noop' => array('singular', 'plural', 'context', 'domain'),
		'_n_js' => array('singular', 'plural', 'domain'),
		'_nx_js' => array('singular', 'plural', 'context', 'domain'),
		'esc_attr__' => array('string', 'domain'),
		'esc_html__' => array('string', 'domain'),
		'esc_attr_e' => array('string', 'domain'),
		'esc_html_e' => array('string', 'domain'),
		'esc_attr_x' => array('string', 'context', 'domain'),
		'esc_html_x' => array('string', 'context', 'domain'),
		'comments_number_link' => array('string', 'singular', 'plural', 'domain'),
	);
	
	// core names their themes differently
	var $exceptions = array( 'twentyten',  'twentyeleven',  'twentytwelve',  'twentythirteen',  'twentyfourteen',  'twentyfifteen',  'twentysixteen',  'twentyseventeen',  'twentyeighteen',  'twentynineteen',  'twentytwenty'  );

	function check( $php_files, $css_files, $other_files ) {
		global $data, $themename;
		
		// ignore core themes for this one check
		if ( !in_array($themename, $this->exceptions) ) {
			$correct_domain = sanitize_title_with_dashes($data['Name']);
			if ( $themename != $correct_domain ) {
				$this->error[] = '<span class="tc-lead tc-warning">' . __( 'WARNING', 'theme-check' ) . '</span>: ' 
					. sprintf ( __( "Your theme appears to be in the wrong directory for the theme name. The directory name must match the slug of the theme. This theme's correct slug and text-domain is <strong>%s</strong>.", 'theme-check' ), $correct_domain );
			}
		}
		
		
		$ret = true;
		$error = '';
		checkcount();

		// make sure the tokenizer is available
		if ( !function_exists( 'token_get_all' ) ) {
			return true;
		}

		$funcs = array_keys($this->rules);
		
		foreach ( $php_files as $php_key => $phpfile ) {
			$error='';
			
			// tokenize the file
			$tokens = token_get_all($phpfile);
			
			$in_func = false;
			$args_started = false;
			$parens_balance = 0;
			$found_domain = false;

			foreach($tokens as $token) {
				$string_success = false;
				
				if (is_array($token)) {
					list($id, $text) = $token;
					if (T_STRING == $id && in_array($text, $funcs)) {
						$in_func = true;
						$func = $text;
						$parens_balance = 0;
						$args_started = false;
						$found_domain = false;
					} elseif (T_CONSTANT_ENCAPSED_STRING == $id) {
						if ($in_func && $args_started) {
							if ($this->rules[$func][$args_count] == 'domain') {
								// strip quotes from the domain, avoids 'domain' and "domain" not being recognized as the same
								$text = str_replace(array('"', "'"), '', $text);
								$domains[] = $text;
								$found_domain=true;
							}
							if ($parens_balance == 1) {
								$args_count++;
								$args[] = $text;
							}
						}
					}
					$token = $text;
				} elseif ('(' == $token){
					if ($parens_balance == 0) {
						$args=array();
						$args_started = true;
						$args_count = 0;
					}
					++$parens_balance;
				} elseif (')' == $token) {
					--$parens_balance;
					if ($in_func && 0 == $parens_balance) {
						if (!$found_domain) {
							$this->error[] = '<span class="tc-lead tc-warning">' . __( 'WARNING', 'theme-check' ) . '</span>: ' 
							. sprintf ( __( 'Found a translation function that is missing a text-domain. Function <strong>%1$s</strong>, with the arguments <strong>%2$s</strong>', 'theme-check' ), 
							$func, implode(',',$args) );
						}
						$in_func = false;
						$func='';
						$args_started = false;
						$found_domain = false;
					}
				}
			}
		}
		
		$domains = array_unique($domains);
		$domainlist = implode( ',', $domains );
		$domainscount = count($domains);
		
		if ( $domainscount > 1 ) {
			$this->error[] = '<span class="tc-lead tc-info">' . __( 'INFO', 'theme-check' ) . '</span>: ' 
			. __( 'More than one text-domain is being used in this theme. This means the theme will not be compatible with WordPress.org language packs.', 'theme-check' )
			. '<br>'
			. sprintf( __( "The domains found are <strong>%s</strong>", 'theme-check'), $domainlist );
		} else {
			$this->error[] = '<span class="tc-lead tc-info">' . __( 'INFO', 'theme-check' ) . '</span>: ' 
			. __( "Only one text-domain is being used in this theme. Make sure it matches the theme's slug correctly so that the theme will be compatible with WordPress.org language packs.", 'theme-check' )
			. '<br>'
			. sprintf( __( "The domain found is <strong>%s</strong>", 'theme-check'), $domainlist );
			
		}
		
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new TextDomainCheck;
