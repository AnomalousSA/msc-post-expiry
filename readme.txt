=== Micro Site Care: Post Expiry ===
Contributors: anomalousdevelopers
Tags: post expiry, content scheduling, auto unpublish, content management
Requires at least: 5.9
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Set per-post expiry dates to automatically unpublish or move posts to an archive category when they become outdated.

== Description ==

Micro Site Care: Post Expiry lets you assign an expiry date to individual posts. A WP cron job (every 15 minutes) processes expired posts and applies the configured action.

* Per-post expiry date via classic editor meta box and Gutenberg sidebar.
* Global default action: Unpublish to Draft or Move to archive category.
* Idempotency flag ensures posts aren't processed twice.
* Per-post expiry log for auditing (last 20 entries).
* Action hook `mscpe_post_expired` for developer integration.
* Filter `mscpe_post_expiry_action` to override the action per post.

Upgrade to **Post Expiry Pro** for draft/private/trash/redirect actions, bulk expiry scheduling, and per-post redirect URL on expiry.

== Installation ==

1. Upload the `msc-post-expiry` folder to `wp-content/plugins/`.
2. Activate through **Plugins > Installed Plugins**.
3. Navigate to **Site Care > Post Expiry** to configure.

== Changelog ==

= 0.1.0 =
* Initial release.
