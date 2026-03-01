=== WP Admin Dark Theme ===
Contributors: Sajjad Amin
Tags: admin, dark theme, admin dark mode, editor styling, dark dashboard, tinymce editor
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Turns the WordPress admin panel into an eye-comfortable dark theme with beautiful UI controls to customize text and background colors natively!

== Description ==

**WP Admin Dark Theme** is an elegant and highly customizable plugin that completely transforms your default White WordPress admin dashboard into a gorgeous "Eye-Comfortable" Dark Mode layout. It replaces harsh pure-blacks with a carefully calibrated "Blue-Grey" palette designed to drastically reduce eye strain for developers and content creators over long working hours.

### Features
* **Global Theme Override:** Instantly styles the WordPress admin dashboard—including links, tables, sidebars, Media toolbars, menus, Add New buttons and more!
* **Toggle Bar Switch:** Features a quick-access lightbulb/moon toggle button squarely in the top Admin Bar to fluidly switch back to light mode whenever you want. 
* **Complete Color Customization:** Visit **Settings > Dark Theme** and utilize native Color Pickers to customize every aspect of the theme (Base Background, Borders, Headings, Hover states, Links, Accent Colors). 
* **TinyMCE Working-Mode UI:** Not a fan of dark mode while actually typing out articles? Launch the **Editor Display UI** directly via a new button inside the post Visual Editor toolbar! Use the dialog's sliders and color pickers to tweak your Post Editor font size, text color, and background dynamically (only in working mode, never affecting live CSS). 

== Installation ==

1. Upload the entire `admin-dark-theme` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access custom configurations via **Settings > Dark Theme**, or toggle it via the top Menu Bar.

== Frequently Asked Questions ==

= Does the Editor's Working UI alter the published output? =
No. Changing text color and background color inside the WP Admin Dark Theme Editor Toolbar Modal applies specifically and locally to your editing screen (`body#tinymce`). It exists *solely* to make typing easier on the eyes.

= Can I restore default colors? =
Yes. There is a "Reset to Defaults" utility provided in both the main Settings Page, and directly on the Editor Modal!

== Changelog ==

= 1.0.0 =
* Initial project release.
* Adds core custom Dark CSS and Native Color Pickers to define specific Hex codes.
* Inserts live "Eye-Comfortable" TinyMCE Modal overrides with UI sliders for developers.
