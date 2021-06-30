=== WP User Manager Groups  ===
Author URI: https://wpusermanager.com
Plugin URI: https://wpusermanager.com
Contributors: wpusermanager
Tags: wpum, wp user manager, newsletter, email, email marketing
Requires at least: 4.7
Tested up to: 5.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable Tag: 1.1.1

A WP User Manager add-on for user Groups

== Description ==

= WP User Manager plugin is required. =

> This is a free add-on for the [WP User Manager plugin](https://wpusermanager.com). You must download and install the [WP User Manager plugin](https://wordpress.org/plugins/wp-user-manager/) before you can use this addon.

== Changelog ==

= 1.1.1 (16th June 2021) =

- Fix: Users added to groups correctly for very large user IDs
- Fix: Fatal error if WP User Manager not active

= 1.1 (23rd April 2021) =

- New: Ability to assign users to groups in bulk from the wp-admin users screen
- New: Allow admins to remove members from the group page
- New: Allow admins export a CSV list of members from the Tools page of the group
- Improvement: Hide users from group listing if their profile privacy is set to hidden
- Fix: Edit group URL redirecting to homepage in some cases
- Fix: Administrator role not added by default to the roles that can create groups
- Fix: Fatal error Uncaught Error: Call to a member function get() on null on some installs

= 1.0.8 (27th January 2021) =

- Fix: Fix PHP Fatal error Uncaught Error: Call to undefined function wpumgp_default_emails()
- Fix: Incorrect plugin slug in .pot file header

= 1.0.7 (21st January 2021) =

- Improvement: Add langauge .pot file for translation of plugin strings
- Fix: Group header loaded at the top of the page when using Yoast SEO

= 1.0.6 (12th January 2021) =

- New: Integration with the Content Restriction addon. Restrict access to content by group membership and membership roles.

= 1.0.5 (23rd Dec 2020) =

- Improvement: WPML addon integration

= 1.0.4 (7th Dec 2020) =

- Fix: Users' groups not appearing on profile groups tab unless the user created the group

= 1.0.3 (6th Dec 2020) =

- Fix: Fatal error when viewing the groups on a profile

= 1.0.2 (5th Dec 2020) =

- New: Ability to set the privacy for a group as hidden
- Fix: Group tab URLs broken on some installs

= 1.0.1 (20th Nov 2020) =

- New: Compatibility with the WPML addon
- Fix: Getting groups by user id not working
- Tweak: added new functions for developers
- Tweak: added new hooks for developers

= 1.0 =

Initial release
