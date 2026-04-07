---
title: Schedule Post Expiry Automatically - MSC Post Expiry Plugin Guide
description: Learn how to automatically expire posts and pages on a scheduled date with MSC Post Expiry. A practical guide to installation, setup, and automation.
keywords: WordPress, post expiry, scheduled posts, content automation, plugin guide
author: Anomalous Developers
date: 2026-04-07
category: Getting Started
plugin: MSC Post Expiry
---

# Schedule Post Expiry Automatically with MSC Post Expiry

## Why Automate Post Expiry?

Managing content lifecycle is crucial for maintaining a healthy, organized WordPress site. Manually archiving or removing old content is time-consuming and easy to forget. MSC Post Expiry automates this process, ensuring your content stays current and organized.

**Benefits of automatic post expiry**:
- **Saves time**: No manual content cleanup needed
- **Maintains freshness**: Automatically remove outdated content
- **Improves organization**: Keep your site clean and organized
- **Reduces clutter**: Remove temporary or seasonal content automatically
- **Better user experience**: Visitors only see current, relevant content
- **Workflow automation**: Integrate expiry into your content strategy

MSC Post Expiry makes it easy to schedule when posts and pages should be removed, archived, or hidden from your site.

## What is MSC Post Expiry?

MSC Post Expiry is a lightweight WordPress plugin that automatically processes posts when they reach a scheduled expiration date and time.

**Key features**:
- Schedule post expiration dates and times
- Choose what happens when posts expire (trash, delete, or draft)
- Configure which post types support expiry
- Automatic processing every 5 minutes
- Comprehensive logging of all actions
- Developer-friendly helper functions
- Works with any WordPress theme
- Free and pro versions available

## Installation

### Step 1: Install the Plugin

1. Go to your WordPress admin dashboard
2. Navigate to **Plugins > Add New**
3. Search for "MSC Post Expiry"
4. Click **Install Now** on the MSC Post Expiry plugin
5. Click **Activate** to enable the plugin

**Alternative**: Download the plugin directly from [WordPress.org plugin directory](https://wordpress.org/plugins/msc-post-expiry/) and upload it to your site.

### Step 2: Access Plugin Settings

Once activated, you'll see a new menu item in your WordPress admin:

1. Go to **Settings > MSC Post Expiry**
2. Click **Settings** to open the configuration page
3. You're ready to configure!

## Basic Configuration

### Enable Post Expiry

1. Open **Settings > MSC Post Expiry**
2. Check the box labeled **"Enable post expiry"**
3. This activates the plugin's automatic processing
4. Click **Save Settings**

### Choose Which Post Types Support Expiry

Decide which content types can have expiration dates:

1. Go to **Settings > MSC Post Expiry**
2. Select **Post type mode**:
   - **Include mode**: Enable expiry only on selected post types
   - **Exclude mode**: Enable expiry on all public types except selected
3. Check the post types you want to support expiry
4. Click **Save Settings**

**Common configurations**:
- **Blog posts only**: Include mode, select "Posts"
- **All content except pages**: Exclude mode, select "Pages"
- **Specific custom types**: Include mode, select your custom post types

### Choose Expiration Action

Decide what happens when a post expires:

1. Go to **Settings > MSC Post Expiry**
2. Select your preferred **Expiry action**:
   - **Move to Trash** (default): Post moves to trash, can be recovered
   - **Permanently Delete**: Post is permanently deleted
   - **Change to Draft**: Post becomes hidden from visitors
3. Click **Save Settings**

**When to use each action**:
- **Trash**: For content you might want to recover later
- **Delete**: For temporary content you won't need again
- **Draft**: For content you want to hide but keep for reference

## Setting Expiration Dates

### For Individual Posts

When editing a post or page:

1. Look for the **Post Expiry** metabox in the sidebar
2. Enter the **Date** (YYYY-MM-DD format)
   - Example: 2026-12-31
3. Enter the **Time** (HH:MM format, 24-hour)
   - Example: 14:30 (2:30 PM)
4. Click **Update** to save the post

The post will be automatically processed when the scheduled time passes.

### Setting Expiration for Multiple Posts

For the pro version, you can set expiration dates for multiple posts at once using bulk actions.

## Common Use Cases

### Temporary Promotional Content

Display special offers or announcements for a limited time:

**Setup**:
- Post type: Posts
- Expiry action: Move to Trash
- Expiry date: End of promotion period

**Example**: Create a "Summer Sale" post that automatically moves to trash on September 1st.

### Time-Sensitive Announcements

Share important announcements that are only relevant for a specific period:

**Setup**:
- Post type: Posts or Pages
- Expiry action: Change to Draft
- Expiry date: When announcement is no longer relevant

**Example**: Event announcements that become drafts after the event ends.

### Seasonal Content

Manage seasonal content that should only be visible during specific times:

**Setup**:
- Post type: Posts or custom post types
- Expiry action: Move to Trash
- Expiry date: End of season

**Example**: Holiday gift guides that automatically disappear after the holidays.

### Event Posts

Archive event posts after the event has passed:

**Setup**:
- Post type: Events (custom post type)
- Expiry action: Change to Draft
- Expiry date: Day after event ends

**Example**: Conference posts that become drafts after the conference concludes.

### Content Rotation

Rotate featured content or homepage content automatically:

**Setup**:
- Post type: Posts or Pages
- Expiry action: Change to Draft
- Expiry date: When new content should take over

**Example**: Featured blog posts that automatically become drafts to make room for new content.

## How Expiration Works

### Automatic Processing

The plugin checks for expired posts every 5 minutes using WordPress cron:

1. **Detection**: Identifies posts with expiry dates in the past
2. **Processing**: Applies the configured expiry action
3. **Logging**: Records all actions in detailed logs
4. **Cleanup**: Removes old logs after 30 days

### Processing Timeline

- **Scheduled time**: 2:30 PM on April 15, 2026
- **Processing window**: Within 5 minutes of scheduled time
- **Result**: Post is processed according to configured action

### Cron Requirements

For automatic processing to work, your WordPress site must have:

- **WordPress cron enabled** (default for most sites)
- **Regular site traffic** (cron checks happen when someone visits your site)
- **Reliable hosting** (some hosts disable WordPress cron)

**If you have unreliable cron**:
Set up a real cron job on your server:
```bash
*/5 * * * * curl https://yoursite.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1
```

## Customization Options

### CSS Styling

The plugin adds CSS classes to styled elements, allowing customization:

```css
/* Style the post expiry metabox */
.mscpe-metabox {
  background-color: #f5f5f5;
  padding: 15px;
  border-radius: 4px;
}

/* Style the date input */
.mscpe-date-input {
  font-size: 14px;
  padding: 8px;
}

/* Style the time input */
.mscpe-time-input {
  font-size: 14px;
  padding: 8px;
}
```

Add these styles to your theme's custom CSS or in a custom CSS plugin.

### Developer Customization

For developers, the plugin provides hooks and filters:

```php
// Hook before processing expired posts
add_action( 'mscpe_before_process_expired_posts', function() {
    // Do something before processing
} );

// Hook after processing expired posts
add_action( 'mscpe_after_process_expired_posts', function( $count ) {
    // Do something after processing
    // $count = number of posts processed
} );

// Check if a post is expired
if ( mscpe_is_post_expired( $post_id ) ) {
    // Post is expired
}

// Get expiry status
$status = mscpe_get_expiry_status( $post_id );
// Returns: "5 days remaining", "Expired", or "No expiry set"
```

## Pro Features

The free version provides all the basics. The **Pro version** adds advanced features:

### Pro Features Include:
- **Bulk expiry setting**: Set expiration dates for multiple posts at once
- **Advanced scheduling**: Create recurring expiry rules
- **Email notifications**: Get notified before posts expire
- **Expiry column**: See expiry dates in post list
- **REST API**: Manage expiry dates programmatically
- **Advanced analytics**: Track expiry actions and trends
- **Priority support**: Get help when you need it

### When to Upgrade to Pro

Consider upgrading to the pro version if you:
- Manage many posts with expiry dates
- Need to set expiry dates for multiple posts at once
- Want email notifications before expiry
- Need REST API access
- Want advanced analytics
- Want priority support

[Learn more about MSC Post Expiry Pro](https://anomalous.co.za/msc-post-expiry-pro/)

## Troubleshooting

### Post Expiry Metabox Not Showing

**Problem**: The metabox doesn't appear when editing posts.

**Solutions**:
1. Check that the plugin is activated (Plugins > Installed Plugins)
2. Go to Settings and verify "Enable post expiry" is checked
3. Verify the post type is enabled in settings
4. Check that you're editing a post type that supports expiry
5. Clear your browser cache and refresh

### Posts Not Expiring Automatically

**Problem**: Posts aren't being processed when the expiry time passes.

**Solutions**:
1. Check that "Enable post expiry" is enabled in settings
2. Verify the expiry date and time are in the past
3. Check that WordPress cron is working (visit your site to trigger cron)
4. Check the logs in `/wp-content/uploads/msc-post-expiry-logs/`
5. Verify your hosting supports WordPress cron

### Expiry Date Not Saving

**Problem**: The expiry date doesn't save when you update the post.

**Solutions**:
1. Verify you're entering the date in YYYY-MM-DD format
2. Verify you're entering the time in HH:MM format (24-hour)
3. Check that you're clicking "Update" to save the post
4. Clear your browser cache and try again
5. Check browser console for JavaScript errors

### Wrong Posts Being Processed

**Problem**: Posts that shouldn't expire are being processed.

**Solutions**:
1. Check the post type configuration in settings
2. Verify the expiry date and time are correct
3. Check the logs to see which posts were processed
4. Review the post type mode (include vs exclude)

## Best Practices

### Content Planning

- **Plan ahead**: Set expiry dates when creating content
- **Document strategy**: Keep notes on your expiry strategy
- **Review regularly**: Check what content is expiring
- **Backup important content**: Save important posts before they expire

### Workflow Integration

- **Use drafts**: Use "Change to Draft" for content you might reuse
- **Use trash**: Use "Move to Trash" for content you might recover
- **Use delete**: Use "Permanently Delete" only for temporary content
- **Consistent strategy**: Use the same action for similar content types

### Monitoring

- **Check logs**: Review logs to see what's being processed
- **Monitor expiry dates**: Keep track of upcoming expirations
- **Test thoroughly**: Test expiry on non-critical posts first
- **Backup regularly**: Backup your site before major expiry events

### Performance

- **Reasonable intervals**: Don't set too many posts to expire at once
- **Monitor cron**: Ensure WordPress cron is working properly
- **Check logs**: Review logs for any errors or issues
- **Optimize database**: Keep your database optimized

## Getting Help

### Documentation
- [Plugin README](https://wordpress.org/plugins/msc-post-expiry/)
- [WordPress.org Support Forum](https://wordpress.org/support/plugin/msc-post-expiry/)

### Support Options
- **Free support**: WordPress.org support forum (community-driven)
- **Pro support**: Priority support included with pro version
- **Contact**: [Support contact information]

### Reporting Issues
If you find a bug or have a feature request:
1. Check the support forum to see if it's already reported
2. Provide detailed information about the issue
3. Include your WordPress version, PHP version, and active plugins
4. Describe steps to reproduce the issue

## Next Steps

Now that you've set up MSC Post Expiry, consider:

1. **Set expiry dates**: Start setting expiration dates on your content
2. **Test thoroughly**: Test on non-critical posts first
3. **Monitor logs**: Check logs to ensure posts are being processed
4. **Optimize workflow**: Integrate expiry into your content strategy
5. **Explore pro features**: Consider upgrading for advanced options
6. **Share feedback**: Let us know what you think!

## About Anomalous Developers

[Anomalous Developers](https://anomalous.co.za) builds practical, focused WordPress plugins that solve real problems. We're committed to quality code, security, and user support.

**Other plugins by Anomalous**:
- [MSC Last Updated](https://wordpress.org/plugins/msc-last-updated/) - Display post update dates
- [MSC External Links](https://wordpress.org/plugins/msc-external-links/) - Manage external links
- [MSC Hub & Spoke SEO](https://wordpress.org/plugins/msc-hub-spoke/) - Build pillar-cluster content

---

**Have questions?** Visit the [WordPress.org support forum](https://wordpress.org/support/plugin/msc-post-expiry/) or contact [support email].

**Like the plugin?** Please leave a review on [WordPress.org](https://wordpress.org/plugins/msc-post-expiry/) to help other users discover it.
