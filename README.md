# Micro Site Care: Post Expiry

Set per-post expiry dates to automatically unpublish or redirect content when it becomes outdated.

## Features

- Per-post expiry date picker (Gutenberg sidebar & classic meta box)
- Configurable expiry actions: Draft, Private, Trash, Archive Category, or Redirect Only
- Optional redirect URL per post when Redirect action is selected
- Lightweight scheduler that can be run via WP-Cron

## Requirements

- WordPress 5.9 or later
- PHP 7.4 or later

## Installation

### Upload via WordPress Admin

1. Compress the plugin folder into a ZIP file (for example `msc-post-expiry.zip`).
2. Go to **Plugins > Add New** in your WordPress admin.
3. Click **Upload Plugin**, choose the ZIP file, and click **Install Now**.
4. After installation click **Activate Plugin**.

### Install via FTP / SFTP

1. Extract the plugin folder and upload the `msc-post-expiry` directory to `wp-content/plugins/` on your server.
2. Visit **Plugins** in the WordPress admin and click **Activate** under *Micro Site Care: Post Expiry*.

## Configuration

Configure expiry behavior at **Site Care > Post Expiry**:

- Default expiry action for posts that reach their expiry timestamp
- Enable or disable automatic redirects and set global redirect defaults
- Configure which post types should support expiry

If the Pro extension is installed you gain bulk scheduling, multi-action expiry, and per-post redirect overrides.

## Usage

- Set an expiry date when editing a post; the configured action will be applied when the timestamp is reached.
- For redirects, enter the destination URL in the post meta when using the Redirect action.

## Cron

The plugin provides a scheduled hook to process expired posts. Ensure WP-Cron is available on your site or run the hook from a server cron job for reliability.

## FAQ

- Q: Can I bulk-schedule expiries?
	- A: Bulk scheduling is provided by the Pro extension.

## Compatibility with Pro

Install `msc-post-expiry-pro` to enable bulk expiry, multiple per-post actions, and redirect behaviors.

## Changelog

### 0.1.0
- Initial release.

## Support

For support and feature requests, open an issue or contact the maintainers.

## License

This plugin is licensed under the GNU General Public License v2 (or later). See the `LICENSE` file for details.

## Development & Linting

This repository contains development tooling (`composer.json`, `package.json`, `phpcs.xml.dist`, `.editorconfig`). These files are not included in packaged ZIPs and are intended for development and linting only. Run `composer install` then `npm run lint` or `npm run lint-fix` in the plugin directory to use the configured PHPCS setup.

---

Micro Site Care — small utilities to keep WordPress sites tidy.
