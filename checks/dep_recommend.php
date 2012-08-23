<?php
// recommended deprecations checks... After some time, these will move into deprecated.php and become required.
class Deprecated_Recommended implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$grep = '';

		$ret = true;

		$checks = array(
		
			// wp-includes deprecated
			array( 'the_editor' => 'wp_editor', '3.3' ),
			array( 'get_user_metavalues' => 'none available', '3.3' ),
			array( 'sanitize_user_object' => 'none available', '3.3' ),
			array( 'get_boundary_post_rel_link' => 'none available', '3.3' ),
			array( 'start_post_rel_link' => 'none available ', '3.3' ),
			array( 'get_index_rel_link' => 'none available', '3.3' ),
			array( 'index_rel_link' => 'none available', '3.3' ),
			array( 'get_parent_post_rel_link' => 'none available', '3.3' ),
			array( 'parent_post_rel_link' => 'none available', '3.3' ),
			array( 'wp_admin_bar_dashboard_view_site_menu' => 'none available', '3.3' ),
			array( 'is_blog_user' => 'is_member_of_blog()', '3.3' ),
			array( 'debug_fopen' => 'error_log()', '3.3' ),
			array( 'debug_fwrite' => 'error_log()', '3.3' ),
			array( 'debug_fclose' => 'error_log()', '3.3' ),

			// wp-admin deprecated
			array( 'screen_layout' => '$current_screen->render_screen_layout()', '3.3' ),
			array( 'screen_options' => '$current_screen->render_per_page_options()', '3.3' ),
			array( 'screen_meta' => ' $current_screen->render_screen_meta()', '3.3' ),
			array( 'media_upload_image' => 'wp_media_upload_handler()', '3.3' ),
			array( 'media_upload_audio' => 'wp_media_upload_handler()', '3.3' ),
			array( 'media_upload_video' => 'wp_media_upload_handler()', '3.3' ),
			array( 'media_upload_file' => 'wp_media_upload_handler()', '3.3' ),
			array( 'type_url_form_image' => 'wp_media_insert_url_form( \'image\' )', '3.3' ),
			array( 'type_url_form_audio' => 'wp_media_insert_url_form( \'audio\' )', '3.3' ),
			array( 'type_url_form_video' => 'wp_media_insert_url_form( \'video\' )', '3.3' ),
			array( 'type_url_form_file' => 'wp_media_insert_url_form( \'file\' )', '3.3' ),
			array( 'add_contextual_help' => 'get_current_screen()->add_help_tab()', '3.3' ),		

			// frontend
			array( 'get_themes' => 'wp_get_themes()', '3.4' ),
			array( 'get_theme' => 'wp_get_theme()', '3.4' ),
			array( 'get_current_theme' => 'wp_get_theme()', '3.4' ),
			array( 'clean_pre' => 'none available', '3.4' ),
			array( 'add_custom_image_header' => 'add_theme_support( \'custom-header\', $args )', '3.4' ),
			array( 'remove_custom_image_header' => 'remove_theme_support( \'custom-header\' )', '3.4' ),
			array( 'add_custom_background' => 'add_theme_support( \'custom-background\', $args )', '3.4' ),
			array( 'remove_custom_background' => 'remove_theme_support( \'custom-background\' )', '3.4' ),
			array( 'get_theme_data' => 'wp_get_theme()', '3.4' ),
			array( 'update_page_cache' => 'update_post_cache()', '3.4' ),
			array( 'clean_page_cache' => 'clean_post_cache()', '3.4' ),
			
			// admin
			array( 'get_allowed_themes' => 'wp_get_themes( array( \'allowed\' => true ) )', '3.4' ),
			array( 'get_broken_themes' => 'wp_get_themes( array( \'errors\' => true )', '3.4' ),
			array( 'current_theme_info' => 'wp_get_theme()', '3.4' ),
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $alt => $check ) {
				checkcount();
				$version = $check;
				$key = key( $check );
				$alt = $check[ $key ];
				if ( preg_match( '/[\s?]' . $key . '\(/', $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = ltrim( rtrim( $matches[0], '(' ) );
					$version = $check[0];
					$grep = tc_grep( $error, $php_key );
					$this->error[] = sprintf(__('<span class="tc-lead tc-recommended">RECOMMENDED</span>: <strong>%1$s</strong> found in the file <strong>%2$s</strong>. Deprecated since version <strong>%3$s</strong>. Use <strong>%4$s</strong> instead.%5$s', 'themecheck'), $error, $filename, $version, $alt, $grep) ;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Deprecated_Recommended;