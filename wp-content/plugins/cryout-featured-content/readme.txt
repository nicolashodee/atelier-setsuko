=== Plugin Name ===
Contributors: Cryout Creations
Tags: theme, cpt, custom post type, featured
Requires at least: 4.5
Tested up to: 5.2.4
Stable tag: 1.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

This is a companion plugin for our themes that adds a custom post type for the theme's landing page boxes, blocks and text areas.

== Description ==

This is a companion plugin for our themes that adds a custom post type for the theme's landing page boxes, blocks and text areas.

== Installation ==

= Automatic installation =

The companion theme is capable of automatically installing the plugin.
1. Navigate to the theme's administration page under Appearance and make sure a valid license key is entered in the License section.
2. In the same administration page switch to the Plugins section and click the Install button next in the Cryout Featured Content box.
3. The plugin will be automatically installed and activated. If you get any error message double-check that the license key is correct.

= Zip installation =

1. Obtain the plugin zip file from your customer account.
2. Navigate to Plugins > Add New in the dashboard.
3. Click Upload Plugin, select the `cryout-featured-content.zip` file and click Install Now
4. Activate the plugin.

= Manual installation =

1. Obtain the plugin zip file from your customer account.
2. Extract the `cryout-featured-content.zip` file on your computer
3. Upload the `cryout-featured-content` folder to your site's `/wp-content/plugins/` directory
4. Activate the plugin from WordPress' plugins list.

== Changelog ==

= 1.2 =
*Release date - 30.10.2018*

* Improved workflow to automatically select the type parameter when adding a new post of specific type
* Revised custom post type and taxonomy registration parameters to enable visibility by multilingual plugins (WPML, Polylang) but hide the posts and categories from public frontend views
* Fixed strict standards warnings: non-static method CryoutFeaturedContent_Blobs::pll_post_types() / CryoutFeaturedContent_Blobs::pll_taxonomies() should not be called statically
* Optimized update check functionality 
* Added GDRP-related privacy policy info
* Fixed some invalid translation textdomains and added .pot file
* Bumped required WP version to 4.5 and PHP to 5.3

= 1.1 =
*Release date - 09.10.2018*

* Added preliminary Polylang multi-language support
* Added dashboard notification for typeless featured content items

= 1.0 = 
*Release date - 04.03.2018*

* Cleaned up dev code

= 0.9 =
*Release date - 13.12.2017*

* Initial release
