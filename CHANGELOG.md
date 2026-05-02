# Changelog

All notable changes to MSC Post Expiry are documented in this file.

## [1.2.0] - 2026-05-01

### Added

- Per-post expiry action override (set a different action for individual posts)
- Custom redirect URLs for expired posts
- "Redirect Only" expiry action (keep post published, redirect visitors)
- Conditional expiry rules engine (trigger actions by category, tag, author, post age, or custom field)
- Multi-step expiry workflows with delayed actions
- Bulk expiry scheduling from the Posts list
- Email notifications before posts expire (configurable recipients and timing)
- SEO handling for expired posts (noindex, nofollow, canonical URL, HTTP status codes)
- Analytics dashboard with Chart.js charts (trends, action breakdown, top categories/authors)
- Action history log (last 50 expiry actions)
- Block editor sidebar panel for setting expiry dates
- SEO, Rules, Workflows, Analytics, and History tabs in settings
- Redirect, bulk scheduling, notification, and logging settings sections
- 15-minute cron schedule for timestamp-based expiry processing
- Database tables for workflows, workflow steps, rules, and analytics

### Changed

- Bumped version to 1.2.0
- All features now included in the single plugin (no separate add-on needed)
- Removed upgrade prompts from Support tab

### Removed

- Removed `is_pro_active()` and `has_feature()` methods (no longer needed)
- Removed upgrade CTA from settings page

## [1.1.0] - 2026-04-13

### Added

- Redesigned settings page with clean tab-based layout
- Added "Change to Private" expiry action
- Added "Move to Category" expiry action with category selector
- Added expiry category option to settings
- Added index.php security file for log directory
- Added comprehensive debug logging for cron processing

### Changed

- Improved cache behavior when WP_DEBUG is enabled

### Fixed

- Fixed time-based expiry (posts now expire at exact scheduled times)
- Fixed log file append issue (now properly appends to existing logs)
- Fixed log file permissions (log files are now readable by web server)
- Fixed WP_Filesystem usage for WordPress.org Plugin Check compliance

## [1.0.0] - 2026-03-26

### Added

- Initial public release
- Post expiry scheduling with date and time
- Three expiration actions: trash, delete, draft
- Post type configuration (include/exclude modes)
- Automatic cron-based processing (every 5 minutes)
- Comprehensive logging with 30-day retention
- Developer helper functions
- Full internationalization support
