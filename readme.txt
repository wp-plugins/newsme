=== News@me ===
Contributors: newsatme
Tags: widget sign up, customized newsletter, customized digest, customized news, automated newsletter, targeted email, email, email subscription, emailing, mailing list, marketing, newsletter, newsletter signup, widget, subscribers, subscription, responsive widget, conversion
Requires at least: 3.5.0
Tested up to: 3.6.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Boost traffic and retain loyal users by reinventing your newsletter.

== Description ==

News@me is a software that simplifies subscriptions to your newsletters by
attracting subscribers in a new way. It creates the newsletter and sends out
the articles for you, it's all automated.

News@me allows you to be less dependent on Google and offers you a new source of loyal traffic. 
You will increase the number of followers of your newsletter because the reader is asked for 
their email in a clear and straightforward way. News@me enhances your content
and sends it out to the readers that are truly interested.

== Installation ==

Upload the News@me plugin to your site. Activate it, then enter your News@me API key.

1, 2, 3: You're done!

== CHANGELOG == 
= 2.1.9 =

* CHANGE: Fix URL's of links in mini global navigation 

= 2.1.8 =

* CHANGE: Add Home and Pricing list item in mini global navigation 

= 2.1.7 =

* CHANGE: activate callout label 

= 2.1.6 =

* CHANGE: plugin now belongs to Plugins section
* CHANGE: plugin removed from Settings section
* ADD: global navigation in plugin pages for better support
* CHANGE: activate account call to action invitation shown in Plugins section
* ADD: logo in headings
* ADD: helper text below API key input field

= 2.1.5 = 
* FIX: bug due to new style php opening tag

= 2.1.4 = 
* FIX: bug related to the retrieval of site_id from API keys

= 2.1.3 = 
* FIX: issue with blank page in form edit when API connection fails

= 2.1.1 = 
* CHANGE: htmlentities replaced with htmlspecialchars for data attributes

= 2.1.0 = 
* CHANGE: boot javascript is now loaded in footer

= 2.0.3 = 
* CHANGE: basic formatting of the Settings elements following WP 3.8.0 release and updates
* REMOVE: meta-box wrap around API Settings elements
* ADD: Link to app 'Sign in' in Plugin Info

= 2.0.2 = 
* CHANGE: load subscription form from remote service
* REMOVE: legacy plugin settings

= 1.1.0 = 
* CHANGE: subscription form priority raised to stay close to the content. 

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
