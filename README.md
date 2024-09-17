# Theme Check

The theme check plugin is an easy way to test your theme and make sure it's up to spec with the latest [theme review](https://make.wordpress.org/themes/handbook/review/) standards. With it, you can run all the same automated testing tools on your theme that WordPress.org uses for theme submissions.

The tests are run through a simple admin menu and all results are displayed at once. This is very handy for theme developers, or anybody looking to make sure that their theme supports the latest WordPress theme standards and practices.

## Frequently Asked Questions

### Why does it flag something as bad?

It's not flagging "bad" things, as such. The theme check is designed to be a non-perfect way to test for compliance with the [Theme Review](https://make.wordpress.org/themes/handbook/review/) guidelines. Not all themes must adhere to these guidelines. The purpose of the checking tool is to ensure that themes uploaded to the central [WordPress.org theme repository](http://wordpress.org/themes/) meet the latest standards of WordPress themes and will work on a wide variety of sites.

Many sites use customized themes, and that's perfectly okay. But themes that are intended for use on many different kinds of sites by the public need to have a certain minimum level of capabilities, in order to ensure proper functioning in many different environments. The Theme Review guidelines are created with that goal in mind.

This theme checker is not perfect, and never will be. It is only a tool to help theme authors, or anybody else who wants to make their theme more capable. All themes submitted to WordPress.org are hand-reviewed by a team of experts. The automated theme checker is meant to be a useful tool only, not an absolute system of measurement.

This plugin does not decide the guidelines used. Any issues with particular theme review guidelines should be discussed on the [Make Themes site](https://make.wordpress.org/themes), or [https://github.com/WPTRT/Theme-Requirements](https://github.com/WPTRT/Theme-Requirements)

## Other Notes

### How to enable trac formatting
The Theme Review team use this plugin while reviewing themes and copy/paste the output into trac tickets, the trac system has its own markup language.
To enable trac formatting in Theme-Check you need to define a couple of variables in wp-config.php:
*TC_PRE* and *TC_POST* are used as a ticket header and footer.
Examples:
```
define( 'TC_PRE', 'Theme Review:[[br]]
- Themes should be reviewed using "define(\'WP_DEBUG\', true);" in wp-config.php[[br]]
- Themes should be reviewed using the test data from the Theme Checklists (TC)
-----
' );
```

```
define( 'TC_POST', 'Feel free to make use of the contact details below if you have any questions,
comments, or feedback:[[br]]
[[br]]
* Leave a comment on this ticket[[br]]
* Send an email to the Theme Review email list[[br]]
* Use the #wordpress-themes IRC channel on Freenode.' );
```

If **either** of these two vars are defined a new trac tickbox will appear next to the *Check it!* button.

If you want to exclude checking other files in development directories return `true` for the filter `tc_skip_development_directories`.

```
add_filter( 'tc_skip_development_directories', '__return_true' );
```

To add more directories to the paths where other files are excluded then add them to the array through the `tc_common_dev_directories` filter.

### Usage with wp-cli

To use with [wp-cli](https://wp-cli.org/), ensure the theme check plugin is active and `wp-cli` is installed. The `theme-check` subcommand is added to `wp-cli` and can be used as follows:

`wp theme-check run [<theme>] [--format=<format>]`

On success, the command returns a formatted table of results from the theme check plugin.

#### Options
| Option | Accepts | Required | Default
| -- | -- | -- | -- | 
| `theme` | The slug of the theme to check | No | Current theme slug
| `format` | `cli` or `json` | No | `cli`

#### Examples
`wp theme-check run`
`wp theme-check run twentytwentyfour`
`wp theme-check run --format=json`
`wp theme-check run twentytwentyfour --format=json`

## Contributors
Otto42, pross, The theme review team
