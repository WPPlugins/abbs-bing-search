=== Bing Search API Integration ===
Contributors: askewbrook
Tags: bing, search
Requires at least: Unknown
Tested up to: 4.8
Stable tag: 0.3.3
License: GPL V3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

== Description ==

A plugin that uses the Bing Search API to replace the default search functionality.

This plugin works out of the box for wordpress default templates, It may require css tweaks to get the desired styling on custom templates.

If you have any enquiries about custom integration of this plugin please contact us as team@askewbrook.com

**Note:** This plugin requires a Bing Cognitive Services API key that can be obtained at <https://www.microsoft.com/cognitive-services/en-us/apis>.

Upon signing up your key will be valid for 90 days, upon which you will need to pay for a subscription to continue using the service.

= Custom Search Functionality =
To use the new custom search functionality provided by bing, you will need to create a new api key & endpoint at <https://customsearch.ai>.

You will then require the `customconfig` code that is generated upon receiving your API endpoint.

An example of a API endpoint would be:
`https://api.cognitive.microsoft.com/bingcustomsearch/v5.0/search?q=microsoft&customconfig=1570678669&responseFilter=Webpages&mkt=en-us&safesearch=Moderate`

of which the code you require to is the `customconfig=########`. place this code into the box provided in the settings and turn on the custom search functionality.

== Installation ==
Unpack and install in your plugin directory. This will create a new search file that will be loaded instead of your theme's existing search.php

== Changelog ==

= 0.3.3 =
* Removed Empty functions from template file

= 0.3.2 =
* Removed Empty functions to allow compatibility with older PHP versions

= 0.3.2 =
* Removed Empty functions to allow compatibility with older PHP versions

= 0.3.1 =
* Updated the installation instructions for new custom search integration.
* Tested plugin up to 4.8

= 0.3 =
* Added implementation of new Bing custom search functionality (currently in preview).

= 0.2 =
* Moved to major version release

= 0.1.3 =
* Added option to allow you to override search website instead of using server name
* Added option to allow an inline search form, for users that don't use sidebars on their theme

= 0.1.25 =
* Added Pot file

= 0.1.2 =
* Added English langage PO files

= 0.1.1 =
* Updated Readme

= 0.1.0 =
* Initial release