# MSC Post Expiry - Installation and Setup Guide

## System Requirements

Before installing MSC Post Expiry, ensure your WordPress site meets these requirements:

- **WordPress**: 5.9 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher
- **Disk space**: At least 1 MB for plugin files and logs
- **WordPress cron**: Enabled (default for most sites)

## Installation Methods

### Method 1: Install from WordPress.org (Recommended)

This is the easiest and most secure method.

1. **Log in to your WordPress admin dashboard**
   - Go to `yoursite.com/wp-admin`
   - Enter your username and password

2. **Navigate to Plugins > Add New**
   - Click "Plugins" in the left sidebar
   - Click "Add New" at the top

3. **Search for "MSC Post Expiry"**
   - Type "MSC Post Expiry" in the search box
   - Wait for search results to load

4. **Install the plugin**
   - Find "MSC Post Expiry" by Anomalous Developers
   - Click "Install Now"
   - Wait for installation to complete

5. **Activate the plugin**
   - Click "Activate" when prompted
   - You'll be redirected to the Plugins page

6. **Verify installation**
   - Look for "MSC Post Expiry" in your Plugins list
   - Status should show "Active"

### Method 2: Manual Installation via Upload

Use this method if you have the plugin ZIP file.

1. **Download the plugin**
   - Download `msc-post-expiry.zip` from WordPress.org
   - Save it to your computer

2. **Log in to your WordPress admin**
   - Go to `yoursite.com/wp-admin`

3. **Navigate to Plugins > Add New**
   - Click "Plugins" in the left sidebar
   - Click "Add New" at the top

4. **Upload the plugin**
   - Click "Upload Plugin" at the top
   - Click "Choose File"
   - Select `msc-post-expiry.zip` from your computer
   - Click "Install Now"

5. **Activate the plugin**
   - Click "Activate Plugin" when prompted

6. **Verify installation**
   - Check that the plugin appears in your Plugins list

### Method 3: Manual Installation via FTP

Use this method if you prefer FTP access.

1. **Download the plugin**
   - Download `msc-post-expiry.zip` from WordPress.org
   - Extract the ZIP file on your computer
   - You should have a folder named `msc-post-expiry`

2. **Connect via FTP**
   - Open your FTP client
   - Connect to your hosting server
   - Navigate to `/wp-content/plugins/`

3. **Upload the plugin folder**
   - Upload the `msc-post-expiry` folder to `/wp-content/plugins/`
   - Wait for upload to complete

4. **Activate the plugin**
   - Log in to your WordPress admin
   - Go to Plugins
   - Find "MSC Post Expiry"
   - Click "Activate"

5. **Verify installation**
   - Check that the plugin is active

## Initial Setup

### Step 1: Access Plugin Settings

After activation, you'll see a new menu item in your WordPress admin:

1. **Click "Settings" in the left sidebar**
2. **Click "MSC Post Expiry"**
3. You're now on the Settings page

### Step 2: Enable the Plugin

1. **Check "Enable post expiry"**
   - This activates the plugin's automatic processing
   - Without this, the plugin won't process expired posts

2. **Click "Save Settings"**
   - You should see a green "Settings saved" notice

### Step 3: Configure Post Types

1. **Choose Post Type Mode**
   - **Include mode**: Enable expiry only on selected post types
   - **Exclude mode**: Enable expiry on all public types except selected

2. **Select Post Types**
   - Check the post types that should support expiry
   - Common choices: Posts, Pages, or both

3. **Click "Save Settings"**

### Step 4: Choose Expiration Action

1. **Select Expiry Action**
   - **Move to Trash** (default): Post moves to trash, can be recovered
   - **Permanently Delete**: Post is permanently deleted
   - **Change to Draft**: Post becomes hidden from visitors

2. **Click "Save Settings"**

### Step 5: Test the Setup

1. **Create a test post**
   - Go to Posts > Add New
   - Enter a title and content
   - Publish the post

2. **Set an expiry date**
   - Look for the "Post Expiry" metabox in the sidebar
   - Enter a date in the past (e.g., yesterday)
   - Enter a time (e.g., 12:00)
   - Click "Update"

3. **Verify processing**
   - Visit your site to trigger WordPress cron
   - Check if the post was processed according to your settings
   - Check the logs in `/wp-content/uploads/msc-post-expiry-logs/`

## Configuration Options Explained

### Enable Post Expiry

**What it does**: Activates the plugin's automatic processing

**When to use**: Always enable this unless you want to temporarily disable the plugin without deactivating it

**Default**: Enabled

### Post Type Mode

**Include Mode**
- **What it does**: Enable expiry only on selected post types
- **When to use**: When you want expiry on specific post types only
- **Example**: Only enable on Posts, not Pages

**Exclude Mode**
- **What it does**: Enable expiry on all public post types except selected
- **When to use**: When you want expiry on most post types
- **Example**: Enable on all types except Pages

**Default**: Include mode

### Post Types

**What it does**: Specifies which post types support expiry

**Available options**:
- Posts
- Pages
- Custom post types (if available)

**Default**: Posts and Pages

### Expiry Action

**Move to Trash**
- **What it does**: Moves expired posts to trash
- **When to use**: For content you might want to recover later
- **Recovery**: Posts can be restored from trash
- **Default**: Yes, this is the default action

**Permanently Delete**
- **What it does**: Permanently deletes expired posts
- **When to use**: For temporary content you won't need again
- **Recovery**: Cannot be recovered (unless you have backups)
- **Warning**: Use with caution!

**Change to Draft**
- **What it does**: Converts expired posts to draft status
- **When to use**: For content you want to hide but keep for reference
- **Recovery**: Posts remain in your database, just hidden
- **Best for**: Seasonal content or posts you might reuse

**Default**: Move to Trash

## Verification Checklist

After setup, verify everything is working:

- [ ] Plugin is activated (Plugins page shows "Active")
- [ ] "Enable post expiry" is checked in settings
- [ ] Post types are selected in settings
- [ ] Expiry action is selected in settings
- [ ] Settings were saved (green notice appeared)
- [ ] Post Expiry metabox appears when editing posts
- [ ] Can enter expiry date and time in metabox
- [ ] Can save posts with expiry dates
- [ ] Logs directory exists: `/wp-content/uploads/msc-post-expiry-logs/`

## Troubleshooting Installation

### Plugin doesn't appear in Plugins list

**Problem**: After uploading, the plugin doesn't appear in your Plugins list.

**Solutions**:
1. Check that the folder is named exactly `msc-post-expiry`
2. Check that files are in `/wp-content/plugins/msc-post-expiry/`
3. Check file permissions (should be 755 for folders, 644 for files)
4. Try deactivating and reactivating other plugins
5. Clear your browser cache

### "Plugin could not be activated" error

**Problem**: You get an error when trying to activate the plugin.

**Solutions**:
1. Check PHP version (must be 7.4 or higher)
2. Check WordPress version (must be 5.9 or higher)
3. Check for PHP errors in your error log
4. Deactivate other plugins and try again
5. Contact your hosting provider for help

### Settings page doesn't load

**Problem**: The settings page shows an error or blank page.

**Solutions**:
1. Check that the plugin is activated
2. Clear your browser cache
3. Try a different browser
4. Check for PHP errors in your error log
5. Deactivate other plugins and try again

### Metabox doesn't appear

**Problem**: The Post Expiry metabox doesn't show when editing posts.

**Solutions**:
1. Check that "Enable post expiry" is checked in settings
2. Check that the post type is selected in settings
3. Check that you're editing a post type that supports expiry
4. Clear your browser cache
5. Try a different browser

## Next Steps

After installation and setup:

1. **Set expiry dates**: Start setting expiration dates on your posts
2. **Test thoroughly**: Test on non-critical posts first
3. **Monitor logs**: Check logs to ensure posts are being processed
4. **Optimize workflow**: Integrate expiry into your content strategy
5. **Explore features**: Learn about all available features

## Getting Help

If you encounter issues:

1. **Check the FAQ**: See if your question is answered
2. **Check the logs**: Review logs in `/wp-content/uploads/msc-post-expiry-logs/`
3. **Visit support forum**: [WordPress.org support forum](https://wordpress.org/support/plugin/msc-post-expiry/)
4. **Contact support**: [Support contact information]

## Security Notes

- The plugin follows WordPress security best practices
- All user input is sanitized
- All output is escaped
- Proper capability checks are in place
- Nonce verification is used for forms
- The plugin uses WordPress APIs for all operations

## Performance Notes

- The plugin has minimal performance impact
- Cron processing happens every 5 minutes
- Database queries are optimized
- Results are cached for 1 hour
- Logs are automatically rotated after 30 days

---

**Ready to get started?** [Learn how to set expiry dates for your posts](../guides/setting-expiry-dates.md)
