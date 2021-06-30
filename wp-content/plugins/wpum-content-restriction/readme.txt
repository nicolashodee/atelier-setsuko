=== WP User Manager Content Restriction ===
Author URI: https://wpusermanager.com
Plugin URI: https://wpusermanager.com
Contributors: wpusermanager
Tags: wpum, wp user manager
Requires at least: 4.7
Tested up to: 5.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable Tag: 1.1.1

A WP User Manager add-on for advanced Content Restriction

== Description ==

= WP User Manager plugin is required. =

> This is an add-on for the [WP User Manager plugin](https://wpusermanager.com). You must download and install the [WP User Manager plugin](https://wordpress.org/plugins/wp-user-manager/) before you can use this addon.

== Changelog ==

= 1.1.1 (19th June 2021) =

- New: Setting for 'restrict everywhere' for all posts in specific post types (posts, pages, custom post types)
- New: Filter 'wpumcr_pre_post_type_{$post->post_type}_restriction' added to allow custom logic for per post type restriction

= 1.1 (9th February 2021) =

- New: Settings to define restriction settings for all posts in specific post types (posts, pages, custom post types)
- New: WooCommerce integration - restrict content to only users who have purchased specific products

= 1.0.4 (24th January 2021) =

- Fix: Undefined property found_posts warning
- Improvement: Add langauge .pot file for translation of plugin strings

= 1.0.3 (9th January 2021) =

- Fix: Archive found posts total not correct breaking pagination
- Improvement: Added 'wpumcr_restrict_content_everywhere' filter control if the post content is hidden in archives when in redirect mode

= 1.0.2 (17th December 2020) =

- Fix: Restricting everywhere doesn't hide from archive pages
- Fix: Redirecting to the homepage causes a loop when viewing the post

= 1.0.1 (2nd December 2020) =

- Improvement: Added filters to restrict all objects of a certain post type
- Improvement: Added hooks and filters for developers to extend the content restriction

= 1.0 =

Initial release
