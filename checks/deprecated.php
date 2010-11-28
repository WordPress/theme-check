<?php

// search for some bad things
class Deprecated implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
$grep = '';

		$ret = true;

		// things to check for
		$checks = array(
			// start wp-includes deprecated
			array('get_post_data' => 'get_post()', '1.5.1'),
			array('start_wp' => 'Use the Loop', '1.5'),
			array('the_category_id' => 'get_the_category()', '0.71'),
			array('the_category_head' => 'get_the_category_by_ID()', '0.71'),
			array('previous_post' => 'previous_post_link()', '2.0'),
			array('next_post' => 'next_post_link()', '2.0'),
			array('user_can_create_post' => 'current_user_can()', '2.0'),
			array('user_can_create_draft' => 'current_user_can()', '2.0'),
			array('user_can_edit_post' => 'current_user_can()', '2.0'),
			array('user_can_delete_post' => 'current_user_can()', '2.0'),
			array('user_can_set_post_date' => 'current_user_can()', '2.0'),
			array('user_can_edit_post_comments' => 'current_user_can()', '2.0'),
			array('user_can_delete_post_comments' => 'current_user_can()', '2.0'),
			array('user_can_edit_user' => 'current_user_can()', '2.0'),
			array('get_linksbyname' => 'get_bookmarks()', '2.1'),
			array('wp_get_linksbyname' => 'wp_list_bookmarks()', '2.1'),
			array('get_linkobjectsbyname' => 'get_bookmarks()', '2.1'),
			array('get_linkobjects' => 'get_bookmarks()', '2.1'),
			array('get_linksbyname_withrating' => 'get_bookmarks()', '2.1'),
			array('get_links_withrating' => 'get_bookmarks()', '2.1'),
			array('get_autotoggle' => 'none available', '2.1'),
			array('list_cats' => 'wp_list_categories', '2.1'),
			array('wp_list_cats' => 'wp_list_categories', '2.1'),
			array('dropdown_cats' => 'wp_dropdown_categories()', '2.1'),
			array('list_authors' => 'wp_list_authors()', '2.1'),
			array('wp_get_post_cats' => 'wp_get_post_categories()', '2.1'),
			array('wp_set_post_cats' => 'wp_set_post_categories()', '2.1'),
			array('get_archives' => 'wp_get_archives', '2.1'),
			array('get_author_link' => 'get_author_posts_url()', '2.1'),
			array('link_pages' => 'wp_link_pages()', '2.1'),
			array('get_settings' => 'get_option()', '2.1'),
			array('permalink_link' => 'the_permalink()', '1.2'),
			array('permalink_single_rss' => 'permalink_rss()', '2.3'),
			array('wp_get_links' => 'wp_list_bookmarks()', '2.1'),
			array('get_links' => 'get_bookmarks()', '2.1'),
			array('get_links_list' => 'wp_list_bookmarks()', '2.1'),
			array('links_popup_script' => 'none available', '2.1'),
			array('get_linkrating' => 'sanitize_bookmark_field()', '2.1'),
			array('get_linkcatname' => 'get_category()', '2.1'),
			array('comments_rss_link' => 'post_comments_feed_link()', '2.5'),
			array('get_category_rss_link' => 'get_category_feed_link()'. '2.5'),
			array('get_author_rss_link' => 'get_author_feed_link()', '2.5'),
			array('comments_rss' => 'get_post_comments_feed_link()', '2.2'),
			array('create_user' => 'wp_create_user()', '2.0'),
			array('gzip_compression' => 'none available', '2.5'),
			array('get_commentdata' => 'get_comment()', '2.7'),
			array('get_catname' => 'get_cat_name()', '2.8'),
			array('get_category_children' => 'get_term_children', '2.8'),
			array('get_the_author_description' => 'get_the_author_meta(\'description\')', '2.8'),
			array('the_author_description' => 'the_author_meta(\'description\')', '2.8'),
			array('get_the_author_login' => 'the_author_meta(\'login\')', '2.8'),
			array('get_the_author_firstname' => 'get_the_author_meta(\'first_name\')', '2.8'),
			array('the_author_firstname' => 'the_author_meta(\'first_name\')', '2.8'),
			array('get_the_author_lastname' => 'get_the_author_meta(\'last_name\')', '2.8'),
			array('the_author_lastname' => 'the_author_meta(\'last_name\')', '2.8'),
			array('get_the_author_nickname' => 'get_the_author_meta(\'nickname\')', '2.8'),
			array('the_author_nickname' => 'the_author_meta(\'nickname\')', '2.8'),
			array('get_the_author_email' => 'get_the_author_meta(\'email\')', '2.8'),
			array('the_author_email' => 'the_author_meta(\'email\')', '2.8'),
			array('get_the_author_icq' => 'get_the_author_meta(\'icq\')', '2.8'),
			array('the_author_icq' => 'the_author_meta(\'icq\')', '2.8'),
			array('get_the_author_yim' => 'get_the_author_meta(\'yim\')', '2.8'),
			array('the_author_yim' => 'the_author_meta(\'yim\')', '2.8'),
			array('get_the_author_msn' => 'get_the_author_meta(\'msn\')', '2.8'),
			array('the_author_msn' => 'the_author_meta(\'msn\')', '2.8'),
			array('get_the_author_aim' => 'get_the_author_meta(\'aim\')', '2.8'),
			array('the_author_aim' => 'the_author_meta(\'aim\')', '2.8'),
			array('get_author_name' => 'get_the_author_meta(\'display_name\')', '2.8'),
			array('get_the_author_url' => 'get_the_author_meta(\'url\')', '2.8'),
			array('the_author_url' => 'the_author_meta(\'url\')', '2.8'),
			array('get_the_author_ID' => 'get_the_author_meta(\'ID\')', '2.8'),
			array('the_author_ID' => 'the_author_meta(\'ID\')', '2.8'),
			array('the_content_rss' => 'the_content_feed()', '2.9'),
			array('make_url_footnote' => 'none available', '2.9'),
			array('_c' => '_x()', '2.9'),
			array('translate_with_context' => '_x()', '3.0'),
			array('nc' => 'nx()', '3.0'),
			array('__ngettext' => '_n_noop()', '2.8'),
			array('__ngettext_noop' => '_n_noop()', '2.8'),
			array('get_alloptions' => 'wp_load_alloptions()', '3.0'),
			array('get_the_attachment_link' => 'wp_get_attachment_link()', '2.5'),
			array('get_attachment_icon_src' => 'wp_get_attachment_image_src()', '2.5'),
			array('get_attachment_icon' => 'wp_get_attachment_image()', '2.5'),
			array('get_attachment_innerhtml' => 'wp_get_attachment_image()', '2.5'),
			array('get_link' => 'get_bookmark()', '2.1'),
			array('sanitize_url' => 'esc_url()', '2.8'),
			array('clean_url' => 'esc_url()', '3.0'),
			array('js_escape' => 'esc_js()', '2.8'),
			array('wp_specialchars' => 'esc_html()', '2.8'),
			array('attribute_escape' => 'esc_attr()', '2.8'),
			array('register_sidebar_widget' => 'wp_register_sidebar_widget()', '2.8'),
			array('unregister_sidebar_widget' => 'wp_unregister_sidebar_widget()', '2.8'),
			array('register_widget_control' => 'wp_register_widget_control()', '2.8'),
			array('unregister_widget_control' => 'wp_unregister_widget_control()', '2.8'),
			array('delete_usermeta' => 'delete_user_meta()', '3.0'),
			array('get_usermeta' => 'get_user_meta()', '3.0'),
			array('update_usermeta' => 'update_user_meta()', '3.0'),
			array('automatic_feed_links' => 'add_theme_support( \'automatic-feed-links\' )', '3.0'),
			array('get_profile' => 'get_the_author_meta()', '3.0'),
			array('get_usernumposts' => 'count_user_posts()', '3.0'),
			array('funky_javascript_callback' => 'none available', '3.0'),
			array('funky_javascript_fix' => 'none available', '3.0'),
			array('is_taxonomy' => 'taxonomy_exists()', '3.0'),
			array('is_term' => 'term_exists()', '3.0'),
			array('is_plugin_page' => '$plugin_page and/or get_plugin_page_hookname() hooks', '3.1'),	
			array('update_category_cache' => 'No alternatives', '3.1'),
			// end wp-includes deprecated
	
			// start wp-admin deprecated
			array('tinymce_include' => 'wp_tiny_mce()', '2.1'),
			array('documentation_link' => 'None available', '2.5'),
			array('wp_shrink_dimensions' => 'wp_constrain_dimensions()','3.0'),
			array('dropdown_categories' => 'wp_category_checklist()','2.6'),
			array('dropdown_link_categories' => 'wp_link_category_checklist()','2.6'),
			array('wp_dropdown_cats' => 'wp_dropdown_categories()','3.0'),
			array('add_option_update_handler' => 'register_setting()','3.0'),
			array('remove_option_update_handler' => 'unregister_setting()','3.0'),
			array('codepress_get_lang' => 'None available','3.0'),
			array('codepress_footer_js' => 'None available','3.0'),
			array('use_codepress' => 'None available','3.0'),
			array('get_author_user_ids' => 'None available','3.1'),
			array('get_editable_authors' => 'None available','3.1'),
			array('get_editable_user_ids' => 'None available','3.1'),
			array('get_nonauthor_user_ids' => 'None available','3.1'),
			array('WP_User_Search' => 'WP_User_Query','3.1'),
			array('get_others_unpublished_posts' => 'None available','3.1'),
			array('get_others_drafts' => 'None available','3.1'),
			array('get_others_pending' => 'None available','3.1'),
			array('register_column_headers' => 'WP_list_table','3.1'),
			array('print_column_headers WP_list_table' => 'None available','3.1')
			// end wp-admin 
			);
			foreach ($php_files as $php_key => $phpfile) {
		foreach ( $checks as $alt => $check) {
		checkcount();
			$version = $check;
			$key = key($check);
			$alt = $check[$key]; 
			if ( preg_match( '/[\s|]' . $key . '\(/m', $phpfile, $matches ) ) {
			    $filename = basename($php_key);
				$error = rtrim($matches[0],'(');
				$version = $check[0];
				$grep = tc_grep( $error, $php_key);
				$this->error[] = "DEPRECATED<strong>{$error}</strong> found in the file <strong>{$filename}</strong>. Deprecated since version <strong>{$version}</strong>. Use <strong>{$alt}</strong> instead.{$grep}";
				$ret = false;
			}
		}
}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Deprecated;


