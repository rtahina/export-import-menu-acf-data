=== Export/Import Menu ACF Data ===
Contributors: r1lita
Tags: menu, menus, export, import, acf, navigation 
Requires at least: 6.9
Tested up to: 7.0
Stable tag: 0.2.2
Requires PHP: 8.0 or higher
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The Export/Import Menu ACF Data plugin allows you to export a given menu with all its attached ACF field values into a JSON file and then import it.

== Description ==

The Export/Import Menu ACF Data plugin is inpired by [Export Import Menus](https://wordpress.org/plugins/export-import-menus/). It allows you to export a given menu with all its attached ACF field values into a JSON file and then import it.

== Frequently Asked Questions ==

= How to Install the plugin =

Clone this repository or [download](https://github.com/rtahina/export-import-menu-acf-data/archive/refs/heads/master.zip) it in a ZIP file.

= Requirements =

* PHP 8.0 or higher
* WordPress 6.9 or higher
* [Advanced Custom Fields (ACF®)](https://wordpress.org/plugins/advanced-custom-fields/) or [Advanced Custom Fields (ACF®) Pro](https://www.advancedcustomfields.com/pro/)

== Changelog ==

= 0.2.3 =
* Updated input sanitation
* Updated the generated filename: admin current time
* Added docs

= 0.2.1 =
* Fixed singelton pattern

= 0.2.0 =
* Added option to allow override if menu already exists on import

= 0.1.0 =
* Export and Import a given menu navigation