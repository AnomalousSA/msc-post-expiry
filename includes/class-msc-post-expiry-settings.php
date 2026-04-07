<?php
/**
 * Admin settings class for MSC Post Expiry.
 *
 * @package MSCPE
 */

namespace MSCPE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings and metabox class.
 */
class Settings {

	/**
	 * Main plugin instance.
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin Plugin instance.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_post_msc-post-expiry_save_settings', array( $this, 'handle_save' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
	}

	/**
	 * Register admin page.
	 */
	public function register_menu() {
		add_options_page(
			esc_html__( 'MSC Post Expiry', 'msc-post-expiry' ),
			esc_html__( 'MSC Post Expiry', 'msc-post-expiry' ),
			'manage_options',
			'mscpe-settings',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Handle settings save.
	 */
	public function handle_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'msc-post-expiry' ) );
		}

		// Verify nonce with better error handling.
		$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'msc-post-expiry_save_settings' ) ) {
			wp_die( esc_html__( 'Security check failed. Please try again.', 'msc-post-expiry' ) );
		}

		$module_enabled = isset( $_POST['module_enabled'] ) ? 1 : 0;
		$post_types     = isset( $_POST['post_types'] ) ? array_values( array_filter( array_map( 'sanitize_key', wp_unslash( (array) $_POST['post_types'] ) ) ) ) : array();
		$post_type_mode = isset( $_POST['post_type_mode'] ) ? sanitize_key( wp_unslash( $_POST['post_type_mode'] ) ) : 'include';
		$expiry_action  = isset( $_POST['expiry_action'] ) ? sanitize_key( wp_unslash( $_POST['expiry_action'] ) ) : 'trash';

		$this->plugin->update_options(
			array(
				'module_enabled' => $module_enabled,
				'post_types'     => $post_types,
				'post_type_mode' => $post_type_mode,
				'expiry_action'  => $expiry_action,
			)
		);

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'mscpe-settings',
					'updated' => '1',
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Render settings page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options = array(
			'module_enabled' => (int) $this->plugin->get_option( 'module_enabled', 1 ),
			'post_types'     => (array) $this->plugin->get_option( 'post_types', array( 'post', 'page' ) ),
			'post_type_mode' => (string) $this->plugin->get_option( 'post_type_mode', 'include' ),
			'expiry_action'  => (string) $this->plugin->get_option( 'expiry_action', 'trash' ),
		);

		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Tab is a safe UI routing parameter.
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'settings';
		if ( ! in_array( $active_tab, array( 'settings', 'usage' ), true ) ) {
			$active_tab = 'settings';
		}

		$tab_url_settings = add_query_arg(
			array(
				'page' => 'mscpe-settings',
				'tab'  => 'settings',
			),
			admin_url( 'options-general.php' )
		);
		$tab_url_usage    = add_query_arg(
			array(
				'page' => 'mscpe-settings',
				'tab'  => 'usage',
			),
			admin_url( 'options-general.php' )
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'MSC Post Expiry', 'msc-post-expiry' ); ?></h1>

			<?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only success notice flag. ?>
			<?php if ( isset( $_GET['updated'] ) && '1' === $_GET['updated'] ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'msc-post-expiry' ); ?></p></div>
			<?php endif; ?>

			<nav class="nav-tab-wrapper">
				<a href="<?php echo esc_url( $tab_url_settings ); ?>" class="nav-tab <?php echo 'settings' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Settings', 'msc-post-expiry' ); ?>
				</a>
				<a href="<?php echo esc_url( $tab_url_usage ); ?>" class="nav-tab <?php echo 'usage' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Usage &amp; Support', 'msc-post-expiry' ); ?>
				</a>
			</nav>

			<?php if ( 'settings' === $active_tab ) : ?>

				<div class="mscpe-settings-layout" style="display:flex;gap:20px;align-items:flex-start;margin-top:1em;">
					<div class="mscpe-settings-sidebar" style="width:240px;flex-shrink:0;order:2;">
						<div class="postbox">
							<div class="postbox-header">
								<h2 class="hndle" style="font-size:14px;padding:8px 12px;">
									<?php esc_html_e( 'Support', 'msc-post-expiry' ); ?>
								</h2>
							</div>
							<div class="inside">
								<p><?php esc_html_e( 'Questions, bugs, or setup help?', 'msc-post-expiry' ); ?></p>
								<a class="button" style="width:100%;text-align:center;box-sizing:border-box;" href="https://anomalous.co.za" target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'Get Support', 'msc-post-expiry' ); ?>
								</a>
							</div>
						</div>
					</div><!-- .mscpe-settings-sidebar -->

					<div class="mscpe-settings-main" style="flex:1;min-width:0;order:1;">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="msc-post-expiry_save_settings" />
							<?php wp_nonce_field( 'msc-post-expiry_save_settings' ); ?>

							<table class="form-table" role="presentation">
								<tbody>
									<tr>
										<th scope="row"><?php esc_html_e( 'Enable post expiry', 'msc-post-expiry' ); ?></th>
										<td>
											<label for="module_enabled">
												<input id="module_enabled" type="checkbox" name="module_enabled" value="1" <?php checked( 1, $options['module_enabled'] ); ?> />
												<?php esc_html_e( 'Allow posts to expire on a scheduled date.', 'msc-post-expiry' ); ?>
											</label>
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="post_type_mode"><?php esc_html_e( 'Post type mode', 'msc-post-expiry' ); ?></label></th>
										<td>
											<select id="post_type_mode" name="post_type_mode">
												<option value="include" <?php selected( 'include', $options['post_type_mode'] ); ?>><?php esc_html_e( 'Enable expiry only on selected post types', 'msc-post-expiry' ); ?></option>
												<option value="exclude" <?php selected( 'exclude', $options['post_type_mode'] ); ?>><?php esc_html_e( 'Enable expiry on all public post types except selected', 'msc-post-expiry' ); ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php esc_html_e( 'Post types', 'msc-post-expiry' ); ?></th>
										<td>
											<fieldset>
												<?php foreach ( $post_types as $post_type ) : ?>
													<label style="display:block;margin-bottom:4px;">
														<input type="checkbox" name="post_types[]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $options['post_types'], true ) ); ?> />
														<?php echo esc_html( $post_type->labels->singular_name ); ?>
														<span style="color:#888;font-size:12px;">(<?php echo esc_html( $post_type->name ); ?>)</span>
													</label>
												<?php endforeach; ?>
											</fieldset>
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="expiry_action"><?php esc_html_e( 'Expiry action', 'msc-post-expiry' ); ?></label></th>
										<td>
											<select id="expiry_action" name="expiry_action">
												<option value="trash" <?php selected( 'trash', $options['expiry_action'] ); ?>><?php esc_html_e( 'Move to Trash', 'msc-post-expiry' ); ?></option>
												<option value="delete" <?php selected( 'delete', $options['expiry_action'] ); ?>><?php esc_html_e( 'Permanently Delete', 'msc-post-expiry' ); ?></option>
												<option value="draft" <?php selected( 'draft', $options['expiry_action'] ); ?>><?php esc_html_e( 'Change to Draft', 'msc-post-expiry' ); ?></option>
											</select>
											<p class="description"><?php esc_html_e( 'What should happen when a post expires.', 'msc-post-expiry' ); ?></p>
										</td>
									</tr>
								</tbody>
							</table>

							<?php
							/**
							 * Renders extension settings inside the shared form (used by Pro).
							 *
							 * @param array<string,mixed> $options Current options.
							 */
							do_action( 'mscpe_settings_sections', $options );
							?>

							<?php submit_button( __( 'Save Settings', 'msc-post-expiry' ) ); ?>
						</form>
					</div><!-- .mscpe-settings-main -->
				</div><!-- .mscpe-settings-layout -->

			<?php elseif ( 'usage' === $active_tab ) : ?>

				<div style="max-width:800px;margin-top:1.5em;">

					<h2><?php esc_html_e( 'How to Use Post Expiry', 'msc-post-expiry' ); ?></h2>
					<p><?php esc_html_e( 'Post Expiry allows you to automatically handle posts when they reach a specified expiration date.', 'msc-post-expiry' ); ?></p>

					<h3><?php esc_html_e( 'Setting an Expiry Date', 'msc-post-expiry' ); ?></h3>
					<p><?php esc_html_e( 'When editing a post or page, look for the "Post Expiry" box in the sidebar on the right. Enter the date and time when you want the post to expire.', 'msc-post-expiry' ); ?></p>

					<h3><?php esc_html_e( 'Expiry Actions', 'msc-post-expiry' ); ?></h3>
					<p><?php esc_html_e( 'When a post expires, one of three actions will occur based on your settings:', 'msc-post-expiry' ); ?></p>
					<ul style="margin-left:20px;">
						<li><strong><?php esc_html_e( 'Move to Trash', 'msc-post-expiry' ); ?></strong> - <?php esc_html_e( 'The post is moved to trash and no longer visible to visitors.', 'msc-post-expiry' ); ?></li>
						<li><strong><?php esc_html_e( 'Permanently Delete', 'msc-post-expiry' ); ?></strong> - <?php esc_html_e( 'The post is permanently deleted from your site.', 'msc-post-expiry' ); ?></li>
						<li><strong><?php esc_html_e( 'Change to Draft', 'msc-post-expiry' ); ?></strong> - <?php esc_html_e( 'The post is changed to draft status and hidden from visitors.', 'msc-post-expiry' ); ?></li>
					</ul>

					<h3><?php esc_html_e( 'Post Type Configuration', 'msc-post-expiry' ); ?></h3>
					<p><?php esc_html_e( 'Use the Settings tab to choose which post types support expiry dates. You can either enable expiry on specific post types or disable it on specific types while enabling it on all others.', 'msc-post-expiry' ); ?></p>

					<h2 style="margin-top:1.5em;"><?php esc_html_e( 'Frequently Asked Questions', 'msc-post-expiry' ); ?></h2>

					<h3><?php esc_html_e( 'The Post Expiry metabox is not showing on my posts.', 'msc-post-expiry' ); ?></h3>
					<ol>
						<li><?php esc_html_e( 'Check that "Enable post expiry" is ticked on the Settings tab.', 'msc-post-expiry' ); ?></li>
						<li><?php esc_html_e( 'Check that the post type (e.g. Post, Page) is selected in the Post types list.', 'msc-post-expiry' ); ?></li>
						<li><?php esc_html_e( 'The metabox appears in the sidebar on the right when editing a post.', 'msc-post-expiry' ); ?></li>
					</ol>

					<h3><?php esc_html_e( 'When does the expiry action occur?', 'msc-post-expiry' ); ?></h3>
					<p><?php esc_html_e( 'Post expiry is processed by WordPress scheduled events (cron). The action will occur shortly after the expiry date and time passes. The exact timing depends on your site traffic and WordPress cron configuration.', 'msc-post-expiry' ); ?></p>

					<h3><?php esc_html_e( 'Can I disable expiry for a specific post?', 'msc-post-expiry' ); ?></h3>
					<p><?php esc_html_e( 'Yes. Simply leave the expiry date and time fields empty in the Post Expiry metabox.', 'msc-post-expiry' ); ?></p>

				</div>

			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Register metabox for post expiry date.
	 */
	public function register_metabox() {
		if ( ! $this->plugin->get_option( 'module_enabled', 1 ) ) {
			return;
		}

		$post_types     = (array) $this->plugin->get_option( 'post_types', array( 'post', 'page' ) );
		$post_type_mode = (string) $this->plugin->get_option( 'post_type_mode', 'include' );

		// Determine which post types should have the metabox.
		$all_post_types = get_post_types( array( 'public' => true ) );
		if ( 'include' === $post_type_mode ) {
			$target_post_types = $post_types;
		} else {
			$target_post_types = array_diff( $all_post_types, $post_types );
		}

		foreach ( $target_post_types as $post_type ) {
			add_meta_box(
				'mscpe-expiry-metabox',
				__( 'Post Expiry', 'msc-post-expiry' ),
				array( $this, 'render_metabox' ),
				$post_type,
				'side',
				'high'
			);
		}
	}

	/**
	 * Render metabox for post expiry date.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_metabox( $post ) {
		wp_nonce_field( 'mscpe_expiry_nonce', 'mscpe_expiry_nonce' );

		$expiry_date = get_post_meta( $post->ID, 'mscpe_expiry_date', true );
		$expiry_time = get_post_meta( $post->ID, 'mscpe_expiry_time', true );
		?>
		<div style="padding: 12px 0;">
			<label for="mscpe_expiry_date" style="display:block;margin-bottom:8px;">
				<strong><?php esc_html_e( 'Expiry Date', 'msc-post-expiry' ); ?></strong>
			</label>
			<input type="date" id="mscpe_expiry_date" name="mscpe_expiry_date" value="<?php echo esc_attr( $expiry_date ); ?>" style="width:100%;padding:6px;box-sizing:border-box;" />

			<label for="mscpe_expiry_time" style="display:block;margin-top:8px;margin-bottom:8px;">
				<strong><?php esc_html_e( 'Expiry Time', 'msc-post-expiry' ); ?></strong>
			</label>
			<input type="time" id="mscpe_expiry_time" name="mscpe_expiry_time" value="<?php echo esc_attr( $expiry_time ); ?>" style="width:100%;padding:6px;box-sizing:border-box;" />

			<p class="description" style="margin-top:8px;font-size:12px;color:#666;">
				<?php esc_html_e( 'Leave empty to disable expiry for this post.', 'msc-post-expiry' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Save metabox data.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post Post object.
	 */
	public function save_metabox( $post_id, $post ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check below.
		if ( ! isset( $_POST['mscpe_expiry_nonce'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified below.
		$nonce = sanitize_text_field( wp_unslash( $_POST['mscpe_expiry_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'mscpe_expiry_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already verified above.
		$expiry_date = isset( $_POST['mscpe_expiry_date'] ) ? sanitize_text_field( wp_unslash( $_POST['mscpe_expiry_date'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already verified above.
		$expiry_time = isset( $_POST['mscpe_expiry_time'] ) ? sanitize_text_field( wp_unslash( $_POST['mscpe_expiry_time'] ) ) : '';

		if ( $expiry_date ) {
			update_post_meta( $post_id, 'mscpe_expiry_date', $expiry_date );
			update_post_meta( $post_id, 'mscpe_expiry_time', $expiry_time );
		} else {
			delete_post_meta( $post_id, 'mscpe_expiry_date' );
			delete_post_meta( $post_id, 'mscpe_expiry_time' );
		}
	}
}
