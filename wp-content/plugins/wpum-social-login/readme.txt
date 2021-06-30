=== WP User Manager Social Login ===
Author URI: https://wpusermanager.com
Plugin URI: https://wpusermanager.com
Contributors: wpusermanager, polevaultweb
Requires at least: 4.7
Tested up to: 5.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable Tag: 2.0.7

Addon for WP User Manager, provides simple and flexible login and registration through social networks.

== Description ==

= WP User Manager plugin is required. =

Addon for WP User Manager, provides simple and flexible login and registration through social networks.

== Changelog ==

= 2.0.7 (23rd April 2021) =

- Improvement: Ensure the display name format set in the main plugin is respected for social registrations
- Improvement: Add 'wpum_after_social_login' filter
- Improvement: Add $owner_details argument to the 'wpum_before_social_login' filter
- Fix: Add support for social login buttons to the Two Factor Authentication login forms with the Security addon

= 2.0.6 (8th March 2021) =

- Fix: Users who are pending approval are able to login

= 2.0.5 (26th February 2021) =

- Improvement: Integration with the User Verification addon. Registrations will respect the user verification setting and need to be manually approved or a verification email sent

= 2.0.4 (29th June 2020) =

- New: Filter 'wpum_social_login_button_text' to allow developers to alter the button text
- Improvement: Owner details passed to 'wpum_after_social_login_registration' where available
- Fix: Twitter callback URL no longer accepting query strings, use /wpum/auth/twitter instead
- Fix: LinkedIn error - you need to pass the scope parameter
- Fix: Fatal error if Facebook Client ID or Secret are entered incorrectly

= 2.0.3 (6th January 2020) =

- New: Compatibility with WP User Manager 2.2 and Registration Forms addon

= 2.0.2 (15th November 2019) =

- Fix: Google error - Something went wrong: Legacy People API has not been used in project

= 2.0.1 =

- Fix: buttons must be an array on first installation
- Fix: issue with hosted domain Google parameter
- Tweak: changed error message when no email is found and required

= 2.0.0 =

- Added compatibility with WP User Manager 2.0.0
- Added support for Twitter
