=== Plugin Name ===
Contributors: nilsringersma
Donate link: https://www.webreact.nl
Tags: postcode, forms
Requires at least: 5.6
Tested up to: 5.6
Stable tag: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plug-in to hook the Postcode.nl API into Ninja Forms.

== Installation Instructions ==
* 1. Install and activate the Plugin.
* 2. Navigate to Ninja Forms -> Setup and enter the Postcode.nl API secret and key.
* 3. From within Ninja Forms editor give the following fields the specified classname:
** 3.1 Zipcode field - api-postcode
** 3.2 Houseno field - api-house_number
** 3.3 Suffix field - api-suffix
** 3.4 Streetname field - api-street_name
** 3.5 City field - api-city
* 4. The plug-in will hook into your form based on those classes.