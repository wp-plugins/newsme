=== News@Me ===
Contributors: newsatme
Tags: tags, community
Requires at least: 3.5.0
Tested up to: 3.6.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Boost traffic and retain loyal users by reinventing your newsletter.

== Description ==

News@Me is a software that simplifies subscriptions to your newsletters by
attracting subscribers in a new way. It creates the newsletter and sends out
the articles for you, it's all automated.

News@Me allows you to be less dependent on Google and offers you a new source of loyal traffic. 
You will increase the number of followers of your newsletter because the reader is asked for 
their email in a clear and straightforward way. News@Me enhances your content
and sends it out to the readers that are truly interested.

== Installation ==

1. Install the plugin in your plugins directory and activate it.
1. Create an account on [News@me](https://app.newsatme.com/users/sign_up)
1. Visit plugin's settings page and install your API key. 
1. Start tagging your articles with the new news@me tags' meta box. 

This plugin works out of the box. You can add further customization on the
plugin's settings page. 

== CHANGELOG == 
= 1.0.7 = 
* ADD: tracking pixel in subscription form to collect conversion stats

= 1.0.6 =
* ADD: WP version and plugin version in API calls
* FIX: articles and subscriptions no longer use verborse params wrapper

= 1.0.5 = 
* ADD: info box in plugin settings sidebar
* FIX: post sync check to be perfomed only in post detail

= 1.0.4 = 
* CHANGE: fix and improve plugin's metadata

= 1.0.3 =
* Fix a translation bug in Italian language printing string in English.

= 1.0.2 =
* Fix a bug in the_content filter for compatibility with older themes.

= 1.0.1 =
* Typo fixed

= 1.0.0 =
* Breaking change, app host changed to app.newsatme.com. 
	
= 0.11.1 =
* Changelog file added to release

= 0.11.0 =
* Removed a conditional to check for the widget to be inside WP's
	`main_query`. This allows the widget to be displayed even inside 
	custom queries inside the template.

= 0.10.10 =
* Check for curl functions to be installed, show an error otherwise.
