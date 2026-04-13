=== MSC Post Expiry ===
Contributors: anomalous
Tags: post-expiry,workflow,content,scheduling,automation
Requires at least: 5.9
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically expire posts and pages on a scheduled date. Set expiration dates in the post editor and let the plugin handle the rest.

== Description ==

MSC Post Expiry allows you to schedule automatic expiration for your posts and pages. Set an expiration date and time, and the plugin will automatically process the post when it expires.

**Features:**

* Schedule post expiration dates and times
* Choose expiration action: move to trash, permanently delete, or convert to draft
* Configure which post types support expiry
* Automatic processing via WordPress cron (every 5 minutes)
* Lightweight and efficient
* Full internationalization support
* Developer-friendly with helper functions
* 12 language translations included

**Use Cases:**

* Temporary promotional content that should disappear automatically
* Time-sensitive announcements
* Seasonal content management
* Event posts that should be archived
* Automatic content cleanup workflows

The plugin adds a "Post Expiry" metabox to your post editor where you can set the expiration date and time. Once the scheduled time passes, the plugin automatically processes the post according to your configured settings.

== Frequently Asked Questions ==

= How do I set an expiration date for a post? =

When editing a post or page, look for the "Post Expiry" box in the sidebar on the right. Enter the date and time when you want the post to expire.

= What happens when a post expires? =

The plugin will perform one of three actions based on your settings:
* **Move to Trash** - The post is moved to trash and no longer visible to visitors
* **Permanently Delete** - The post is permanently deleted from your site
* **Change to Draft** - The post is changed to draft status and hidden from visitors

= How often does the plugin check for expired posts? =

The plugin checks for expired posts every 5 minutes using WordPress cron. This is a reasonable interval that balances responsiveness with server load.

= Can I choose which post types support expiry? =

Yes! Go to Settings > MSC Post Expiry to configure which post types support expiration. You can either enable expiry on specific post types or disable it on specific types while enabling it on all others.

= Is there a way to check if a post is expired? =

Yes, developers can use the helper functions:
* `mscpe_is_post_expired( $post_id )` - Check if a post is expired
* `mscpe_get_expiry_datetime( $post_id )` - Get the expiry date and time
* `mscpe_get_expiry_status( $post_id )` - Get human-readable expiry status
* `mscpe_format_expiry_datetime( $date, $time )` - Format expiry date for display

= Does this plugin work with custom post types? =

Yes! You can configure the plugin to work with any public post type, including custom post types.

= Will expired posts be permanently deleted? =

Only if you configure the plugin to permanently delete them. By default, expired posts are moved to trash, which allows you to recover them if needed.

= What languages are supported? =

The plugin includes translations for 12 languages: German (Germany and Switzerland), Spanish (Spain and Mexico), French (France and Canada), Italian, Japanese, Dutch (Netherlands and Belgium), and Portuguese (Portugal and Brazil).

== Installation ==

1. Upload the plugin to `/wp-content/plugins/`.
2. Activate in the WordPress plugins screen.
3. Go to Settings > MSC Post Expiry to configure the plugin.
4. When editing a post or page, use the "Post Expiry" metabox to set expiration dates.

== Changelog ==

= 1.1.0 =
* Redesigned settings page with clean tab-based layout
* Added "Support" tab with help resources and Pro upgrade CTA
* Renamed "Usage & Support" tab to "Usage"
* Added "Change to Private" expiry action
* Added "Move to Category" expiry action with category selector
* Added expiry category option to settings

= 1.0.0 =
* Initial release
* Post expiry scheduling with date and time
* Three expiration actions: trash, delete, draft
* Post type configuration (include/exclude modes)
* Automatic cron-based processing
* Comprehensive logging
* Developer helper functions
* Full internationalization support

== Upgrade Notice ==

= 1.1.0 =
New features: "Change to Private" and "Move to Category" expiry actions. Redesigned settings page with improved UI. Added Support tab with help resources.

= 1.0.0 =
Initial release of MSC Post Expiry. Schedule automatic expiration for your posts and pages.
