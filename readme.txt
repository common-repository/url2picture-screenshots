=== URL2Picture Screenshots ===
Contributors: Daniel Herken
Tags: screenshots, url2picture, images
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: 1.0
License: GPLv2

This plugins allows to easily embed thumbnails or full-page screenshots of any website taken in any major browser into your Wordpress site. 

== Description ==

This plugins allows to easily embed thumbnails or full-page screenshots taken in any major browser of any website into your blog. It uses the service [URL2Picture](http://www.url2picture.com) to generate the screenshots. 

The plugin includes a caching functionality and allows to configure viewport-size, thumbnail-size, screenshot delay and screenshot cropping.

Basic usage

You can use a shortcut to embed screenshots into any post:

[url2picture browser=CHROME32 width=VIEWPORT_WIDTH height=VIEWPORT_HEIGHT thumbnail_width=THUMBNAIL_WIDTH thumbnail_height=THUMBNAIL_HEIGHT delay=DELAY crop_height=CROP_HEIGHT crop_width=CROP_WIDTH]www.wordpress.org[/url2picture]

Examples

A few usage examples:

[url2picture]www.wordpress.com[/url2picture]
This shortcut will display a orginal-size and full-page screenshot of wordpress.com

[url2picture thumbnail_width=300]www.wordpress.com[/url2picture]
This shortcut will display a 300 pixel wide thumbnail of www.wordpress.com

[url2picture thumbnail_width=300 crop_height=100 delay=1000]www.wordpress.com[/url2picture]
This shortcut will display a 300 pixel wide thumbnail cropped to a height of 100 pixel. The screenshot will be taken 1000ms after page load is finished.

== Installation ==

The installation is simple: 

1. Upload `url2picture-screenshots.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Open the 'URL2Picture' settings menu and enter API-Key and Secret
4. Optional: Activate or deactivate the cache function in the settings menu
5. Start adding the shortcut [url2picture] to your posts

If you use the cache please make sure that the folder 'wp-content/url2picture' is writeable.

== Changelog ==

= 1.0 =
* Initial plugin: Settings page, screenshot API, cache functionality

== Frequently Asked Questions ==

= What features are planned in future versions? =

* Please send any ideas, bug reports or feature request at dherken@browseemall.com

= More information =
Visit [URL2Picture](http://www.url2picture.com)