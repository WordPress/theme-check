=== Theme-Check ===
Contributors: pross, Otto42
Author URI: http://www.pross.org.uk
Plugin URL: http://www.pross.org.uk/plugins
Requires at Least: 3.0
Tested Up To: 3.1
Tags: template, theme, check, checker, tool, wordpress, wordpress.org, upload, uploader, test, guideline, review
Stable tag: 20101228.1

A simple and easy way to test your theme for all the latest WordPress standards and practices. A great theme development tool!

== Description ==

The theme check plugin is an easy way to test your theme and make sure it's up to spec with the latest [theme review](http://codex.wordpress.org/Theme_Review) standards. With it, you can run all the same automated testing tools on your theme that WordPress.org uses for theme submissions.

The tests are run through a simple admin menu and all results are displayed at once. This is very handy for theme developers, or anybody looking to make sure that their theme supports the latest WordPress theme standards and practices.

== Frequently Asked Questions ==

= What's with the version numbers? =

The version number is the date of the revision of the [guidelines](http://codex.wordpress.org/Theme_Review) used to create it.

= Why does it flag something as bad? =

It's not flagging "bad" things, as such. The theme check is designed to be a non-perfect way to test for compliance with the [http://codex.wordpress.org/Theme_Review](Theme Review) guidelines. Not all themes must adhere to these guidelines. The purpose of the checking tool is to ensure that themes uploaded to the central [http://wordpress.org/extend/themes/](WordPress.org theme repository) meet the latest standards of WordPress themes and will work on a wide variety of sites. 

Many sites use customized themes, and that's perfectly okay. But themes that are intended for use on many different kinds of sites by the public need to have a certain minimum level of capabilities, in order to ensure proper functioning in many different environments. The Theme Review guidelines are created with that goal in mind.

This theme checker is not perfect, and never will be. It is only a tool to help theme authors, or anybody else who wants to make their theme more capable. All themes submitted to WordPress.org are hand-reviewed by a team of experts. The automated theme checker is meant to be a useful tool only, not an absolute system of measurement.

This plugin does not decide the guidelines used. Any issues with particular theme review guidelines should be discussed on the [http://lists.wordpress.org/mailman/listinfo/theme-reviewers](Theme Reviewers mailing list).

== Changelog ==

= 20101228.1 =
* Fix embed_defaults filter check and stylesheet file data check.

= 20101226.1 = 
* Whole system redesign to allow easier synching with WordPress.org uploader. Many other additions/subtractions/changes as well. 
* WordPress 3.1 guidelines added, to help theme authors ensure compatibility for upcoming release.

= 20101110.7 =
* Re-added malware.php checks for fopen and file_get_contents (INFO)
* fixed a couple of undefined index errors.

= 20101110.4_r2 =
* Fixed Warning: Wrong parameter count for stristr() 

= 20101110.4_r1 =
* Added `echo` to suggested.php

= 20101110.4 =
* Fixed deprecated function call to get_plugins()

= 20101110.3 =
* Fixed undefined index. 

= 20101110.2 =
* Missing `<` in main.php
* Added conditional checks for licence.txt OR Licence tags in style.css
* UI improvements.

= 20101110.1 =
* Date fix!

= 10112010_r1 =
* Fixed hardcoded links check. Added FAQ

= 10112010 =
* First release.
