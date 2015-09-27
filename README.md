# WP API Extensions
Collection of extensions to the Wordpress REST API (WP API)
under development as part of a [project for refugees in Germany](http://vmkrcmar21.informatik.tu-muenchen.de/wordpress/)

The following routes are added:
* `extensions/v0/modified_content/posts_and_pages?since=<datetime>` returns all modified posts and pages
   since the given datetime (in the [ISO8601 format `Y-m-dTG:i:sZ`](http://php.net/manual/en/class.datetime.php#datetime.constants.atom),
   for instance `2015-09-20T15:37:25Z`)
* `extensions/v0/languages/wpml` returns the languages available through the WPML plugin
* `extensions/v0/multisites/` returns the multisites of the network

## Installation
Go to Plugins > Add New > Upload and upload this repository's zip
