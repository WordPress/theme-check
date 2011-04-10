<?php
class WormCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
$php_files = array_merge( $php_files, $other_files );
		$checks = array(
			'/wshell\.php/'=> __( 'This may be a script used by hackers to get control of your server!', 'themecheck' ),
			'/ShellBOT/' => __( 'This may be a script used by hackers to get control of your server', 'themecheck' ),
			'/uname -a/' => __( 'Tells a hacker what operating system your server is running', 'themecheck' ),
			'/YW55cmVzdWx0cy5uZXQ=/' => __( 'base64 encoded text found in Search Engine Redirect hack <a href="http://blogbuildingu.com/wordpress/wordpress-search-engine-redirect-hack">[1]</a>', 'themecheck' ),
			'/\$_COOKIE\[\'yahg\'\]/' => __( 'YAHG Googlerank.info exploit code <a href="http://creativebriefing.com/wordpress-hacked-googlerankinfo/">[1]</a>', 'themecheck' ),
			'/ekibastos/' => __( 'Possible Ekibastos attack <a href="http://ocaoimh.ie/did-your-wordpress-site-get-hacked/">[1]</a>', 'themecheck' ),
			'/<!--[A-Za-z0-9]+--><\?php/' => __( 'Symptom of a link injection attack <a href="http://www.kyle-brady.com/2009/11/07/wordpress-mediatemple-and-an-injection-attack/">[1]</a>', 'themecheck' ),
			'/<script>\/\*(GNU GPL|LGPL)\*\/ try\{window.onload.+catch\(e\) \{\}<\/script>/' => __( 'Possible "Gumblar" JavaScript attack <a href="http://threatinfo.trendmicro.com/vinfo/articles/securityarticles.asp?xmlfile=042710-GUMBLAR.xml">[1]</a> <a href="http://justcoded.com/article/gumblar-family-virus-removal-tool/">[2]</a>', 'themecheck' ),
			'/php \$[a-zA-Z]*=\'as\';/' => __( 'Symptom of the "Pharma Hack" <a href="http://blog.sucuri.net/2010/07/understanding-and-cleaning-the-pharma-hack-on-wordpress.html">[1]</a>', 'themecheck' ),
			'/defined?\(\'wp_class_support/' => __( 'Symptom of the "Pharma Hack" <a href="http://blog.sucuri.net/2010/07/understanding-and-cleaning-the-pharma-hack-on-wordpress.html">[1]</a>', 'themecheck' ),
			'/AGiT3NiT3NiT3fUQKxJvI/' => __( 'Malicious footer code injection detected!', 'themecheck' )
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = $matches[0];
					$grep = tc_grep( $error, $php_key );
					$this->error[] = "<span class='tc-lead tc-warning'>WARNING</span>: <strong>{$filename}</strong> {$check}{$grep}";
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new WormCheck;
