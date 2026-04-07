# MSC Post Expiry

Automatically expire posts and pages on a scheduled date. Set expiration dates in the post editor and let the plugin handle the rest.

## Overview

MSC Post Expiry allows you to schedule automatic expiration for your posts and pages. Set an expiration date and time, and the plugin will automatically process the post when it expires.

### Features

- **Schedule Post Expiration**: Set expiration dates and times via metabox in the post editor
- **Flexible Expiration Actions**: Choose what happens when a post expires:
  - Move to Trash
  - Permanently Delete
  - Convert to Draft
- **Post Type Configuration**: Enable expiry on specific post types or all public post types
- **Automatic Processing**: Uses WordPress cron to process expired posts every 5 minutes
- **Comprehensive Logging**: Detailed logs of all expiry actions with 30-day retention
- **Developer-Friendly**: Helper functions for developers to check expiry status
- **Lightweight**: Minimal performance impact
- **Internationalization Ready**: Full translation support

### Use Cases

- Temporary promotional content that should disappear automatically
- Time-sensitive announcements and news
- Seasonal content management
- Event posts that should be archived
- Automatic content cleanup workflows
- Scheduled content rotation

## Installation

### From WordPress.org (Recommended)

1. Go to **Plugins > Add New** in your WordPress admin
2. Search for "MSC Post Expiry"
3. Click **Install Now**
4. Click **Activate**
5. Go to **Settings > MSC Post Expiry** to configure

### Manual Installation

1. Download the plugin ZIP file
2. Upload the `msc-post-expiry` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** screen
4. Go to **Settings > MSC Post Expiry** to configure

## Configuration

### Settings Page

Navigate to **Settings > MSC Post Expiry** to configure:

1. **Enable Post Expiry**: Toggle to enable/disable the plugin
2. **Post Type Mode**: Choose to enable expiry on:
   - Specific post types (Include mode)
   - All public post types except selected (Exclude mode)
3. **Post Types**: Select which post types support expiry
4. **Expiry Action**: Choose what happens when posts expire:
   - Move to Trash (default)
   - Permanently Delete
   - Convert to Draft

### Setting Expiration Dates

When editing a post or page:

1. Look for the **Post Expiry** metabox in the sidebar
2. Enter the expiration **Date** (YYYY-MM-DD format)
3. Enter the expiration **Time** (HH:MM format, 24-hour)
4. Click **Update** to save

The post will be processed automatically when the scheduled time passes.

## Developer Guide

### Helper Functions

The plugin provides helper functions for developers:

#### `mscpe_get_expiry_datetime( $post_id )`

Get the expiry date and time for a post.

```php
$expiry = mscpe_get_expiry_datetime( $post_id );
if ( $expiry ) {
    echo 'Expires on: ' . $expiry['date'] . ' at ' . $expiry['time'];
}
```

**Returns**: Array with 'date' and 'time' keys, or false if not set.

#### `mscpe_is_post_expired( $post_id )`

Check if a post is expired.

```php
if ( mscpe_is_post_expired( $post_id ) ) {
    echo 'This post has expired!';
}
```

**Returns**: Boolean (true if expired, false otherwise).

#### `mscpe_get_expiry_status( $post_id )`

Get human-readable expiry status.

```php
echo mscpe_get_expiry_status( $post_id );
// Output: "5 days remaining" or "Expired" or "No expiry set"
```

**Returns**: String with expiry status.

#### `mscpe_format_expiry_datetime( $date, $time )`

Format expiry date and time for display.

```php
$formatted = mscpe_format_expiry_datetime( '2026-04-15', '14:30' );
echo $formatted; // Formatted according to WordPress date/time settings
```

**Returns**: Formatted datetime string.

### Hooks and Filters

#### `mscpe_before_process_expired_posts`

Fires before processing expired posts.

```php
add_action( 'mscpe_before_process_expired_posts', function() {
    // Do something before processing
} );
```

#### `mscpe_after_process_expired_posts`

Fires after processing expired posts.

```php
add_action( 'mscpe_after_process_expired_posts', function( $processed_count ) {
    // Do something after processing
    // $processed_count = number of posts processed
} );
```

#### `mscpe_before_expire_post`

Fires before expiring a single post.

```php
add_action( 'mscpe_before_expire_post', function( $post_id, $action ) {
    // Do something before expiring
    // $post_id = post ID
    // $action = expiry action (trash, delete, draft)
}, 10, 2 );
```

#### `mscpe_after_expire_post`

Fires after expiring a single post.

```php
add_action( 'mscpe_after_expire_post', function( $post_id, $action ) {
    // Do something after expiring
}, 10, 2 );
```

#### `mscpe_pro_active`

Filter to indicate if pro version is active.

```php
add_filter( 'mscpe_pro_active', function() {
    return true; // Indicates pro version is active
} );
```

### Logging

The plugin logs all expiry actions to:

```
/wp-content/uploads/msc-post-expiry-logs/
```

Log files are automatically rotated after 30 days.

## Requirements

- **WordPress**: 5.9 or higher
- **PHP**: 7.4 or higher
- **License**: GPL-2.0+

## Frequently Asked Questions

### How often does the plugin check for expired posts?

The plugin checks for expired posts every 5 minutes using WordPress cron. This is a reasonable interval that balances responsiveness with server load.

### What happens if my site doesn't have reliable cron?

If your WordPress cron is not working properly, expired posts may not be processed on time. You can set up a real cron job to trigger WordPress cron:

```bash
*/5 * * * * curl https://yoursite.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1
```

### Can I change the expiry action after setting it?

Yes! You can change the expiry action in the settings page at any time. The new action will apply to all future expirations.

### Will expired posts be permanently deleted?

Only if you configure the plugin to permanently delete them. By default, expired posts are moved to trash, which allows you to recover them if needed.

### Does this work with custom post types?

Yes! You can configure the plugin to work with any public post type, including custom post types.

### Can I extend the plugin?

Yes! The plugin provides hooks and filters for developers to extend functionality. See the Developer Guide section above.

## Troubleshooting

### The Post Expiry metabox is not showing

1. Check that "Enable post expiry" is enabled in Settings
2. Check that the post type is selected in the post types list
3. Make sure you're editing a post type that supports expiry

### Posts are not expiring automatically

1. Check that "Enable post expiry" is enabled in Settings
2. Verify that WordPress cron is working (check if `wp-cron.php` is being called)
3. Check the logs in `/wp-content/uploads/msc-post-expiry-logs/`
4. Verify the expiry date and time are in the past

### I see errors in the logs

Check the log files in `/wp-content/uploads/msc-post-expiry-logs/` for detailed error messages. Common issues:

- Post type not found
- Post not found
- Invalid expiry action

## Support

For support, questions, or feature requests, please visit:

- **Website**: https://anomalous.co.za
- **WordPress.org**: https://wordpress.org/plugins/msc-post-expiry/

## Development

### Setup

```bash
# Install dependencies
composer install
npm install

# Run linters
composer lint
npm run lint

# Fix linting issues
composer lint-fix
npm run lint-fix
```

### Code Standards

This plugin follows:

- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WordPress Plugin Security Best Practices](https://developer.wordpress.org/plugins/security/)
- [WordPress Plugin Internationalization](https://developer.wordpress.org/plugins/internationalization/)

### Testing

To test the plugin:

1. Install on a local WordPress installation
2. Configure settings
3. Create test posts with expiry dates
4. Verify posts expire correctly
5. Check logs for any errors

### File Structure

```
msc-post-expiry/
├── msc-post-expiry.php           # Main plugin file
├── uninstall.php                 # Cleanup on uninstall
├── readme.txt                    # WordPress.org readme
├── README.md                     # Development readme
├── CHANGELOG.md                  # Version history
├── LICENSE                       # GPL-2.0+ license
├── includes/
│   ├── class-msc-post-expiry.php          # Main plugin class
│   ├── class-msc-post-expiry-settings.php # Settings and metabox
│   ├── class-mscpe-cron.php               # Cron processing
│   └── class-msc-post-expiry-module.php   # Frontend module
├── languages/
│   └── msc-post-expiry.pot       # Translation template
├── assets/
│   ├── css/
│   │   ├── admin-components.css  # Admin UI components
│   │   └── admin-tokens.css      # Admin UI tokens
│   └── js/
│       └── expiry-sidebar.js     # Metabox JavaScript
└── vendor/                       # Composer dependencies (dev only)
```

## Changelog

### Version 1.0.0 (April 7, 2026)

**Initial Release**

- Post expiry scheduling with date and time
- Three expiration actions: trash, delete, draft
- Post type configuration (include/exclude modes)
- Automatic cron-based processing (every 5 minutes)
- Comprehensive logging with 30-day retention
- Developer helper functions
- Full internationalization support
- WordPress Coding Standards compliant
- Security best practices implemented

## License

This plugin is licensed under the [GPL-2.0+ License](LICENSE).

## Credits

**Developed by**: [Anomalous Developers](https://anomalous.co.za)

## Contributing

We welcome contributions! Please feel free to submit issues or pull requests.

---

**Status**: Production Ready  
**Version**: 1.0.0  
**Last Updated**: April 7, 2026
