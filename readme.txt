=== wpTwit ===
Contributors: chrisshennan
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CUZZSGQGJJ95E
Tags: twitter, sidebar, plugin, widget, feed, rss, social
Requires at least: 2.8
Tested up to: 3.0.4
Stable Tag: 1.0.1


Add your twitter posts to your blog using wpTwit - A PHP Twitter Reader based on lineTwit (an extension of myTwit by Ralph Slooten)

== Description ==

This plugin displays the twitter posts from a twitter account in the sidebar.

wpTwit is a PHP Twitter Reader based on [lineTwit](http://www.chrisshennan.com/my-projects/linetwit-a-php-twitter-reader-based-on-mytwit/) which was written for [Line Digital](http://www.line.uk.com) and based upon [myTwit](http://www.axllent.org/projects/mytwit) by Ralph Slooten.

wpTwit was written by [Chris Shennan](http://www.chrisshennan.com).

== Installation ==

1. Upload the wpTwit directory to your '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure your sidebar widget in Apprearance -> Widgets

== Screenshots ==

1. The sidebar widget configuration screen

== Frequently Asked Questions ==

= Why am I receiving Twitter API limit exceeded messages? =

This is most often due to write permissions.  Please ensure write permission have been applied to the folder the cache files are to be created in.  

The default for this is /wp-content/uploads/

wpTwit will create a sub-directory called twitter to store the cache files required for this plugin.

= I am having problems getting this plugin to work =

You can contact me via [my website](http://www.chrisshennan.com/contact-me/) for support.

== Changelog ==

= 1.0 =
* Initial Release

= 1.0.1 =
* Fixing some short php tag problems.