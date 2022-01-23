=== Embed Bokun ===
Contributors: luuptek
Tags: bokun
Requires at least: 5.0
Tested up to: 5.8
Requires PHP: 5.6
Stable tag: 0.22
License: GPLv2

Embed Bokun allows you a possibility to add Bokun products (bokun.io) to your WordPress site easily via Gutenberg block.

== Description ==
Embed Bokun allows to you add Bokun products to your website via Gutenberg blocks.

To be able to use the plugin, you need to have Gutenberg active (WordPress version 5.0 and higher and classic editor not installed).

Plugin is extreme developer friendly allowing to create custom Bokun product block via few actions and filters.

More details and documentation about the plugin can be found from Github: [https://github.com/luuptek/embed-bokun](https://github.com/luuptek/embed-bokun)

== Installation ==
1. Activate the plugin
2. If you want to use custom layout for the bokun products, proceed to Settings => Bokun settings to update access key, secret key and booking channel ID.
3. In page/post edit screen you can add Bokun product block. For block settings, add product ID and booking channel UUID, if you are using default widget.
4. If you want to use custom style widget, you need to enter bokun ID found under Document-tab. Plugin will fetch data via Bokun API hourly to update the data in posts where Bokun ID is defined.

== Changelog ==
0.22 fix on image base url
0.21 create new setting to include js in front end or not, only load js if needed there (performance issue)
0.2 Add padding options for the bokun blocks
0.1.1 Add new filter related to api path, few new functions
0.1 First version to public
