# Setting Expiry Dates - Complete Guide

## Overview

Setting expiry dates is the core functionality of MSC Post Expiry. This guide walks you through everything you need to know about setting, managing, and monitoring expiry dates.

## Before You Start

Make sure you have:

1. **Plugin installed and activated**
   - Go to Plugins and verify MSC Post Expiry is active

2. **Plugin configured**
   - Go to Settings > MSC Post Expiry
   - Verify "Enable post expiry" is checked
   - Verify your post type is selected

3. **Post type enabled**
   - The post type you want to expire must be selected in settings

## Setting Expiry Dates

### For New Posts

When creating a new post:

1. **Go to Posts > Add New**
   - Or your custom post type

2. **Create your post content**
   - Enter title and content as usual
   - Add featured image if desired

3. **Look for Post Expiry metabox**
   - On the right sidebar
   - Below the Publish box

4. **Enter expiry date**
   - Format: YYYY-MM-DD
   - Example: 2026-12-31
   - Click the date field to use a date picker

5. **Enter expiry time**
   - Format: HH:MM (24-hour)
   - Example: 14:30 (2:30 PM)
   - Example: 09:00 (9:00 AM)

6. **Publish the post**
   - Click "Publish"
   - Post is now scheduled to expire

### For Existing Posts

To add expiry to an existing post:

1. **Go to Posts**
   - Click "Posts" in the left sidebar

2. **Edit the post**
   - Click the post title or "Edit"

3. **Look for Post Expiry metabox**
   - On the right sidebar

4. **Enter expiry date and time**
   - Follow the same format as above

5. **Update the post**
   - Click "Update"
   - Expiry date is now set

### Removing Expiry Dates

To remove an expiry date from a post:

1. **Edit the post**
   - Go to Posts and click "Edit"

2. **Clear the date field**
   - Delete the date from the Date field
   - Leave it blank

3. **Update the post**
   - Click "Update"
   - Expiry date is now removed

## Date and Time Format

### Date Format

**Required format**: YYYY-MM-DD

**Examples**:
- 2026-04-15 (April 15, 2026)
- 2026-12-31 (December 31, 2026)
- 2027-01-01 (January 1, 2027)

**Invalid formats** (will not work):
- 04/15/2026 (US format)
- 15-04-2026 (European format)
- April 15, 2026 (Text format)

### Time Format

**Required format**: HH:MM (24-hour)

**Examples**:
- 09:00 (9:00 AM)
- 12:00 (12:00 PM / Noon)
- 14:30 (2:30 PM)
- 23:59 (11:59 PM)

**Invalid formats** (will not work):
- 2:30 PM (12-hour with AM/PM)
- 14:30:00 (with seconds)
- 2:30 (missing leading zero)

### Using the Date Picker

Most modern browsers provide a date picker:

1. **Click the date field**
   - A calendar should appear
   - Select the date you want
   - Date is automatically formatted

2. **If no date picker appears**
   - Type the date manually
   - Use YYYY-MM-DD format

## Common Expiry Scenarios

### Scenario 1: Promotional Post

You're running a summer sale and want the post to disappear after the sale ends.

**Setup**:
- **Date**: 2026-09-01 (September 1, 2026)
- **Time**: 00:00 (midnight)
- **Action**: Move to Trash

**Why this setup**:
- Post disappears at the start of the day
- Can be recovered from trash if needed
- Clean cutoff at midnight

### Scenario 2: Event Announcement

You're announcing an event and want the post to become a draft after the event.

**Setup**:
- **Date**: 2026-06-15 (June 15, 2026)
- **Time**: 17:00 (5:00 PM)
- **Action**: Change to Draft

**Why this setup**:
- Post becomes hidden after event ends
- Remains in database for reference
- Can be reused for future events

### Scenario 3: Temporary Notice

You're posting a temporary notice that should be deleted after a week.

**Setup**:
- **Date**: 2026-04-14 (April 14, 2026)
- **Time**: 12:00 (noon)
- **Action**: Permanently Delete

**Why this setup**:
- Post is completely removed
- No need to keep it
- Clean removal from database

### Scenario 4: Seasonal Content

You're posting seasonal content that should disappear at the end of the season.

**Setup**:
- **Date**: 2026-12-26 (December 26, 2026)
- **Time**: 23:59 (11:59 PM)
- **Action**: Move to Trash

**Why this setup**:
- Post disappears at the very end of the day
- Can be recovered and reused next year
- Automatic cleanup

## Bulk Setting Expiry Dates

**Note**: Bulk expiry setting is a pro feature. In the free version, you must set expiry dates individually.

For the pro version:

1. **Go to Posts**
2. **Select multiple posts**
   - Check the boxes next to posts
3. **Choose "Set Expiry Date" from bulk actions**
4. **Enter the expiry date and time**
5. **Apply**

## Monitoring Expiry Dates

### Viewing Expiry Dates

In the free version, you can see expiry dates by:

1. **Editing the post**
   - The expiry date appears in the metabox

2. **Checking the logs**
   - Go to `/wp-content/uploads/msc-post-expiry-logs/`
   - View log files to see what was processed

**Pro version** adds:
- Expiry date column in post list
- Quick view of all expiry dates
- Upcoming expiry notifications

### Checking Logs

The plugin logs all expiry actions:

1. **Access your site via FTP or file manager**
2. **Navigate to `/wp-content/uploads/msc-post-expiry-logs/`**
3. **Open the latest log file**
4. **View the log entries**

**Log format**:
```
[2026-04-07 14:30:15] Post ID 123 expired with action "trash".
[2026-04-07 14:30:20] Post ID 456 expired with action "draft".
[2026-04-07 14:35:00] Processed 2 expired posts.
```

### Understanding Log Entries

**Successful expiry**:
```
Post ID 123 expired with action "trash".
```
- Post was successfully processed
- Action was applied

**Processing summary**:
```
Processed 5 expired posts.
```
- Total posts processed in this run
- Appears at the end of each processing cycle

**No expired posts**:
```
No expired posts found.
```
- No posts were expired at this time
- Normal if no posts have reached their expiry time

## Troubleshooting

### Post didn't expire at scheduled time

**Possible causes**:
1. WordPress cron is not working
2. No one visited your site at the scheduled time
3. Date or time format is incorrect
4. Post type is not enabled in settings

**Solutions**:
1. Check that WordPress cron is enabled
2. Visit your site to trigger cron
3. Verify date format (YYYY-MM-DD)
4. Verify time format (HH:MM, 24-hour)
5. Check settings to verify post type is enabled

### Date field shows error

**Possible causes**:
1. Date format is incorrect
2. Date is in the future (for some browsers)
3. Browser doesn't support date input

**Solutions**:
1. Use YYYY-MM-DD format
2. Try a different browser
3. Type the date manually

### Can't find Post Expiry metabox

**Possible causes**:
1. Plugin is not activated
2. "Enable post expiry" is not checked
3. Post type is not selected in settings
4. Browser cache issue

**Solutions**:
1. Check that plugin is activated
2. Go to Settings and check "Enable post expiry"
3. Select your post type in settings
4. Clear browser cache and refresh

### Post expired but action didn't happen

**Possible causes**:
1. WordPress cron hasn't run yet
2. Post type is not enabled
3. Expiry date/time is incorrect
4. Hosting doesn't support WordPress cron

**Solutions**:
1. Visit your site to trigger cron
2. Check settings to verify post type is enabled
3. Verify date and time are in the past
4. Check logs for errors
5. Contact hosting provider about cron

## Best Practices

### Planning Expiry Dates

- **Set dates when creating content**: Don't forget to set expiry dates
- **Use consistent times**: Use the same time for all expirations (e.g., midnight)
- **Plan ahead**: Set expiry dates well in advance
- **Document your strategy**: Keep notes on your expiry strategy

### Managing Expiry Dates

- **Review regularly**: Check upcoming expirations
- **Test thoroughly**: Test on non-critical posts first
- **Monitor logs**: Check logs to ensure posts are being processed
- **Backup important content**: Save important posts before they expire

### Choosing Actions

- **Trash**: For content you might recover later
- **Draft**: For content you want to hide but keep
- **Delete**: Only for temporary content you won't need

### Avoiding Issues

- **Don't set too many at once**: Avoid setting many posts to expire at the same time
- **Use realistic times**: Set times when your site gets traffic
- **Monitor cron**: Ensure WordPress cron is working
- **Check permissions**: Ensure you have permission to edit posts

## Advanced Tips

### Setting Expiry for Content Series

For a series of posts that should expire together:

1. **Create all posts**
2. **Set the same expiry date and time**
3. **All posts expire together**

### Rotating Featured Content

To rotate featured content automatically:

1. **Create new featured post**
2. **Set expiry date for old featured post**
3. **Old post becomes draft automatically**
4. **New post takes over**

### Seasonal Content Management

For seasonal content:

1. **Create seasonal posts**
2. **Set expiry date for end of season**
3. **Posts automatically disappear**
4. **Reuse next year by changing status back to published**

### Archiving Old Content

To archive old content:

1. **Set expiry action to "Draft"**
2. **Posts become hidden but remain in database**
3. **Can be republished later if needed**
4. **Keeps site clean while preserving content**

## Getting Help

If you have questions about setting expiry dates:

1. **Check this guide**: Most questions are answered here
2. **Check the FAQ**: See if your question is answered
3. **Check the logs**: Review logs for any errors
4. **Visit support forum**: [WordPress.org support forum](https://wordpress.org/support/plugin/msc-post-expiry/)
5. **Contact support**: [Support contact information]

---

**Next**: [Learn about the plugin settings](../getting-started/installation-and-setup.md)
