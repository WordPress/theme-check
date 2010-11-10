<?php

// search for some bad things
class Deprecated implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {
$grep = '';

		$ret = true;

		// things to check for
		$checks = array(
			'get_post_data' => 'get_post()',
			'start_wp' => 'Use the Loop',
			'the_category_id' => 'get_the_category()',
			'the_category_head' => 'get_the_category_by_ID()',
			'previous_post' => 'previous_post_link()',
			'next_post' => 'next_post_link()',
			'user_can_create_post' => 'current_user_can()',
			'user_can_create_draft' => 'current_user_can()',
			'user_can_edit_post' => 'current_user_can()',
			'user_can_delete_post' => 'current_user_can()',
			'user_can_set_post_date' => 'current_user_can()',
			'user_can_edit_post_comments' => 'current_user_can()',
			'user_can_delete_post_comments' => 'current_user_can()',
			'user_can_edit_user' => 'current_user_can()',
			'get_linksbyname' => 'get_bookmarks()',
			'wp_get_linksbyname' => 'wp_list_bookmarks()',
			'get_linkobjectsbyname' => 'get_bookmarks()',
			'get_linkobjects' => 'get_bookmarks()',
			'get_linksbyname_withrating' => 'get_bookmarks()',
			'get_links_withrating' => 'get_bookmarks()',
			'get_autotoggle' => 'none available',
			'list_cats' => 'wp_list_categories',
			'wp_list_cats' => 'wp_list_categories',
			'dropdown_cats' => 'wp_dropdown_categories()',
			'list_authors' => 'wp_list_authors()',
			'wp_get_post_cats' => 'wp_get_post_categories()',
			'wp_set_post_cats' => 'wp_set_post_categories()',
			'get_archives' => 'wp_get_archives',
			'get_author_link' => 'get_author_posts_url()',
			'link_pages' => 'wp_link_pages()',
			'get_settings' => 'get_option()',
			'permalink_link' => 'the_permalink()',
			'permalink_single_rss' => 'permalink_rss()',
			'wp_get_links' => 'wp_list_bookmarks()',
			'get_links' => 'get_bookmarks()',
			'get_links_list' => 'wp_list_bookmarks()',
			'links_popup_script' => 'none available',
			'get_linkrating' => 'sanitize_bookmark_field()',
			'get_linkcatname' => 'get_category()',
			'comments_rss_link' => 'get_category_feed_link()',
			'get_author_rss_link' => 'get_author_feed_link()',
			'comments_rss' => 'get_post_comments_feed_link()',
			'create_user' => 'wp_create_user()',
			'gzip_compression' => 'none available',
			'get_commentdata' => 'get_comment()',
			'get_catname' => 'get_cat_name()',
			'get_category_children' => 'get_term_children',
			'get_the_author_description' => 'get_the_author_meta(\'description\')',
			'the_author_description' => 'the_author_meta(\'description\')',
			'get_the_author_login' => 'the_author_meta(\'login\')',
			'get_the_author_firstname' => 'get_the_author_meta(\'first_name\')',
			'the_author_firstname' => 'the_author_meta(\'first_name\')',
			'get_the_author_lastname' => 'get_the_author_meta(\'last_name\')',
			'the_author_lastname' => 'the_author_meta(\'last_name\')',
			'get_the_author_nickname' => 'get_the_author_meta(\'nickname\')',
			'the_author_nickname' => 'the_author_meta(\'nickname\')',
			'get_the_author_email' => 'get_the_author_meta(\'email\')',
			'the_author_email' => 'the_author_meta(\'email\')',
			'get_the_author_icq' => 'get_the_author_meta(\'icq\')',
			'the_author_icq' => 'the_author_meta(\'icq\')',
			'get_the_author_yim' => 'get_the_author_meta(\'yim\')',
			'the_author_yim' => 'the_author_meta(\'yim\')',
			'get_the_author_msn' => 'get_the_author_meta(\'msn\')',
			'the_author_msn' => 'the_author_meta(\'msn\')',
			'get_the_author_aim' => 'get_the_author_meta(\'aim\')',
			'the_author_aim' => 'the_author_meta(\'aim\')',
			'get_author_name' => 'get_the_author_meta(\'display_name\')',
			'get_the_author_url' => 'get_the_author_meta(\'url\')',
			'the_author_url' => 'the_author_meta(\'url\')',
			'get_the_author_ID' => 'get_the_author_meta(\'ID\')',
			'the_author_ID' => 'the_author_meta(\'ID\')',
			'the_content_rss' => 'the_content_feed()',
			'make_url_footnote' => 'none available',
			'_c' => '_x()',
			'translate_with_context' => '_x()',
			'nc' => 'nx()',
			'__ngettext_noop' => '_n_noop()',
			'get_alloptions' => 'wp_load_alloptions()',
			'get_the_attachment_link' => 'wp_get_attachment_link()',
			'get_attachment_icon_src' => 'wp_get_attachment_image_src()',
			'get_attachment_icon' => 'wp_get_attachment_image()',
			'get_attachment_innerhtml' => 'wp_get_attachment_image()',
			'get_link' => 'get_bookmark()',
			'sanitize_url' => 'esc_url()',
			'clean_url' => 'esc_url()',
			'js_escape' => 'esc_js()',
			'wp_specialchars' => 'esc_html()',
			'attribute_escape' => 'esc_attr()',
			'register_sidebar_widget' => 'wp_register_sidebar_widget()',
			'unregister_sidebar_widget' => 'wp_unregister_sidebar_widget()',
			'register_widget_control' => 'wp_register_widget_control()',
			'unregister_widget_control' => 'wp_unregister_widget_control()',
			'delete_usermeta' => 'delete_user_meta()',
			'get_usermeta' => 'get_user_meta()',
			'update_usermeta' => 'update_user_meta()',
			'automatic_feed_links' => 'add_theme_support( \'automatic-feed-links\' )',
			'get_profile' => 'get_the_author_meta()',
			'get_usernumposts' => 'count_user_posts()',
			'funky_javascript_callback' => 'none available',
			'funky_javascript_fix' => 'none available',
			'is_taxonomy' => 'taxonomy_exists()',
			'is_term' => 'term_exists()',
			'is_plugin_page' => '$plugin_page and/or get_plugin_page_hookname() hooks'
			);
		foreach ($php_files as $php_key => $phpfile) {
		foreach ($checks as $key => $check) {
		checkcount();
			if ( preg_match( '/[\s|]' . $key . '\(/m', $phpfile, $matches ) ) {
			    $filename = basename($php_key);
				$error = rtrim($matches[0],'(');

$grep = tc_grep( $error, $php_key);
				$this->error[] = "DEPRECATED<strong>{$error}</strong> found in the file <strong>{$filename}</strong>.<br />Use <strong>{$check}</strong> instead.{$grep}";


				$ret = false;
			}


		}

}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Deprecated;


