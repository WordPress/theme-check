# General checks

Checks that are performed on all types of themes.

## Required

### Admin menu

Check that themes do not add admin menu items other than
add_theme_page, add_menu_page, add_submenu_page.

<https://github.com/WordPress/theme-check/blob/master/checks/admin_menu.php>

### Bad things

Checks that the theme does not include:

- `eval()`
- PHP system calls: popen, proc_open, exec, shell_exec, system, passthru
- `ini_set`
- `base64_decode()`, `base64_encode()`, `uudecode()`, `str_rot13()`
- Google search and advertising codes
- Affiliate codes

<https://github.com/WordPress/theme-check/blob/master/checks/badthings.php>

### CDN

Checks that the theme does not include:

- bootstrapcdn.com
- kit.fontawesome.com
- googlecode.com/svn/
- maxcdn.com
- code.jquery.com/jquery-
- aspnetcdn.com
- cloudflare.com
- keycdn.com
- pxgcdn.com
- vimeocdn.com

All resources needs to be included in the theme. See https://make.wordpress.org/themes/handbook/review/required/#stylesheets-and-scripts

<https://github.com/WordPress/theme-check/blob/master/checks/cdn.php>

### Deprecated

Checks that the theme does not include deprecated functions.
See the file for the full list.

<https://github.com/WordPress/theme-check/blob/master/checks/deprecated.php>

Checks that the theme does not use deprecated function parameters.
See the file for the full list.

<https://github.com/WordPress/theme-check/blob/master/checks/more_deprecated.php>

### Directories

Check that the theme does not include the following directories:

- git,
- svn,
- hg,
- bzr',

These directories must not be in the production version of the theme.

<https://github.com/WordPress/theme-check/blob/master/checks/directories.php>

### Escaping links

Checks that home_url() and get_template_directory_uri are not echoed directly without escaping.

<https://github.com/WordPress/theme-check/blob/master/checks/escaping.php>

### Favicon

Checks that the theme does not include its own favicon.

<https://github.com/WordPress/theme-check/blob/master/checks/favicon.php>

### Filenames

Checks that the theme includes the minimum files index.php, style.css, readme.txt.

Checks that the theme does not include the following:

- Hidden Files or Folders
- wpml-config.xml
- loco.xml
- thumbs.db
- desktop.ini
- project.xml
- .kpf
- php.ini
- dwsync.xml
- error_log
- web.config
- .sql
- __MACOSX
- .lubith
- .wie
- .dat
- phpcs.xml.dist
- phpcs.xml
- .xml
- .sh
- postcss.config.js
- .editorconfig.
- .stylelintrc\.json
- .eslintrc
- favicon\.ico

Configuration files meant for development must not be in the production version of the theme.

<https://github.com/WordPress/theme-check/blob/master/checks/filenames.php>

### Genereated files

Check that the theme does not include generated files:

Artisteer

- art_normalize_widget_style_tokens
- art_include_lib
- _remove_last_slash($url) {
- adi_normalize_widget_style_tokens
- m_normalize_widget_style_tokens
- `bw = '<!--- BEGIN Widget --->';`
- `ew = '<!-- end_widget -->';`
- `end_widget' => '<!-- end_widget -->'`

Lubith

- Lubith

Templatetoaster

- templatetoaster_
- Templatetoaster_
- `@package templatetoaster`

wpthemegenerator

- wptg_

Generated themes are not allowed in the themes directory.

<https://github.com/WordPress/theme-check/blob/master/checks/generated.php>

### Includes plugin

Checks that the theme does not include a zip file.

<https://github.com/WordPress/theme-check/blob/master/checks/included-plugins.php>

### Line endings

Checks that not both DOS and UNIX style line endings are used in the theme.
This causes a problem with SVN repositories. Only use one style of line endings.

<https://github.com/WordPress/theme-check/blob/master/checks/lineendings.php>

### Non GPL sites

Checks that the theme does not reference assets from websites that offers assets that are not compatible with GPL.

- unsplash
- pixabay
- freeimages
- photopin
- splitshire
- freepik
- flaticon
- pikwizard
- stock.adobe
- elements.envato
- undraw.co

<https://github.com/WordPress/theme-check/blob/master/checks/nongplsites.php>

### Plugin territory

Checks that the theme does not include plugin territory items:

- register_post_type
- register_taxonomy
- wp_add_dashboard_widget
- register_block_type
- add_shortcode
- mime_types
- upload_mimes
- user_contactmethods

And does not remove non-presentational hooks:

- 'wp_generator', <https://developer.wordpress.org/reference/functions/wp_generator/>
- 'feed_links', <https://developer.wordpress.org/reference/functions/feed_links/>
- 'feed_links_extra', <https://developer.wordpress.org/reference/functions/feed_links_extra/>
- 'print_emoji_detection_script', <https://developer.wordpress.org/reference/functions/print_emoji_detection_script/>
- 'wp_resource_hints', <https://developer.wordpress.org/reference/functions/wp_resource_hints/>
- 'adjacent_posts_rel_link_wp_head', <https://developer.wordpress.org/reference/functions/adjacent_posts_rel_link_wp_head/>
- 'wp_shortlink_wp_head', <https://developer.wordpress.org/reference/functions/wp_shortlink_wp_head/>
- '_admin_bar_bump_cb', <https://developer.wordpress.org/reference/functions/_admin_bar_bump_cb/>
- 'rsd_link', <https://developer.wordpress.org/reference/functions/rsd_link/>
- 'rest_output_link_wp_head', <https://developer.wordpress.org/reference/functions/rest_output_link_wp_head/>
- 'wp_oembed_add_discovery_links', <https://developer.wordpress.org/reference/functions/wp_oembed_add_discovery_links/>
- 'wp_oembed_add_host_js', <https://developer.wordpress.org/reference/functions/wp_oembed_add_host_js/>
- 'rel_canonical', <https://developer.wordpress.org/reference/functions/rel_canonical/>
- print_emoji_styles
- print_emoji_detection_script
- rest_output_link_header
- wp_shortlink_header
- redirect_canonical

<https://github.com/WordPress/theme-check/blob/master/checks/plugin-territory.php>

### Screenshot

Checks that the screenshot size and aspect ratio is correct.

<https://github.com/WordPress/theme-check/blob/master/checks/screenshot.php>

### Script tags

Checks that themes do not include `<script>` tags.
Scripts and styles needs to be enqueued or added via a hook, not hard coded.

<https://github.com/WordPress/theme-check/blob/master/checks/script_tag.php>

## Warning

### Escaping

Checks that get_theme_mod is not echoed directly without escaping.
Checks that esc_attr is not used outside HTML attributes, and that esc_html is not used inside HTML attributes.

<https://github.com/WordPress/theme-check/blob/master/checks/escaping.php>

### Admin bar

Check that themes are not filtering show_admin_bar to hide it,
or hides it by styling #wpadminbar.

<https://github.com/WordPress/theme-check/blob/master/checks/adminbar.php>

### Deregister

Check that the theme does not deregister core scripts.

<https://github.com/WordPress/theme-check/blob/master/checks/deregister.php>

### Non-printable characters

Checks that the theme does not include non-printable characters.

<https://github.com/WordPress/theme-check/blob/master/checks/nonprintable.php>

### PHP short tags

Checks that the theme does not include PHP short tags.

<https://github.com/WordPress/theme-check/blob/master/checks/phpshort.php>

### User levels in menus

Check that user levels are not used when creating menus. User levels are deprecated.

<https://github.com/WordPress/theme-check/blob/master/checks/admin_menu.php>

## Recommended

### Block patterns

Check if the theme registers block patterns and block styles.

<https://github.com/WordPress/theme-check/blob/master/checks/block-patterns.php>

### Content width

Checks that the theme adds a content_width.

<https://github.com/WordPress/theme-check/blob/master/checks/content-width.php>

### I18n Internationalization

Check that variables are not used in the gettext translations functions.
Translation function calls must NOT contain PHP variables.

<https://github.com/WordPress/theme-check/blob/master/checks/i18n.php>

## Info

### Iframes

Check if the theme includes iframes. Iframes need to be manually reviewed.
Using iframes to display remote content is not allowed.

<https://github.com/WordPress/theme-check/blob/master/checks/iframes.php>

### Include

Checks if the theme uses include or require and informats that templates
should be included using get_template_part instead.

<https://github.com/WordPress/theme-check/blob/master/checks/include.php>

### Links

Checks if the theme includes hardcoded links in PHP files that are neither theme- or author URI.

<https://github.com/WordPress/theme-check/blob/master/checks/links.php>

