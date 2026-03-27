<?php
/**
 * Admin settings class for MSC Post Expiry.
 */

namespace MSC_Post_Expiry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	}

	/**
	 * Register admin page.
	 */
	public function register_menu() {
		add_submenu_page(
			'msc-site-care',
			'MSC Post Expiry',
			'MSC Post Expiry',
			'manage_options',
			'msc-post-expiry',
			array( $this, 'render_page' )
		);

		if ( true && true ) {
			$this->maybe_register_upgrade_submenu();
		}
	}

	/**
	 * Register one contextual upgrade submenu for free plugin only.
	 */
	private function maybe_register_upgrade_submenu() {
		global $submenu;

		$upgrade_slug       = 'msc-site-care-upgrade';
		$already_registered = false;

		if ( ! empty( $submenu['msc-site-care'] ) ) {
			foreach ( $submenu['msc-site-care'] as $item ) {
				if ( isset( $item[2] ) && $upgrade_slug === $item[2] ) {
					$already_registered = true;
					break;
				}
			}
		}

		if ( $already_registered ) {
			return;
		}

		add_submenu_page(
			'msc-site-care',
			esc_html__( 'Upgrade to Pro', 'msc-post-expiry' ),
			esc_html__( 'Upgrade to Pro', 'msc-post-expiry' ),
			'manage_options',
			$upgrade_slug,
			array( $this, 'render_upgrade_page' )
		);
	}

	/**
	 * Handle settings save.
	 */
	public function handle_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'msc-post-expiry' ) );
		}

		check_admin_referer( 'msc-post-expiry_save_settings' );

		$module_enabled = isset( $_POST['module_enabled'] ) ? 1 : 0;
		$this->plugin->update_options( array( 'module_enabled' => $module_enabled ) );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'msc-post-expiry',
					'updated' => '1',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Render settings page.
	 */
	public function render_page() {
		$options = array(
			'module_enabled' => (int) $this->plugin->get_option( 'module_enabled', 1 ),
		);
		?>
		<div class="wrap msc-admin-wrap">
			<div class="msc-admin-header">
				<h1><?php echo esc_html( 'MSC Post Expiry' ); ?></h1>
			</div>
			<div class="msc-admin-card">
				<?php if ( isset( $_GET['updated'] ) ) : ?>
					<div class="msc-admin-notice">
						<?php echo esc_html__( 'Settings updated.', 'msc-post-expiry' ); ?>
					</div>
				<?php endif; ?>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="msc-post-expiry_save_settings" />
					<?php wp_nonce_field( 'msc-post-expiry_save_settings' ); ?>

					<div class="msc-admin-form-row">
						<label for="module_enabled">
							<input id="module_enabled" type="checkbox" name="module_enabled" value="1" <?php checked( 1, $options['module_enabled'] ); ?> />
							<?php echo esc_html__( 'Enable module', 'msc-post-expiry' ); ?>
						</label>
					</div>

					<button type="submit" class="msc-admin-button msc-admin-button-primary">
						<?php echo esc_html__( 'Save Settings', 'msc-post-expiry' ); ?>
					</button>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Render contextual upgrade page for free variant.
	 */
	public function render_upgrade_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap msc-admin-wrap">
			<div class="msc-admin-header">
				<h1><?php echo esc_html__( 'Upgrade to Pro', 'msc-post-expiry' ); ?></h1>
			</div>
			<div class="msc-admin-card">
				<p><?php echo esc_html__( 'Unlock advanced features with the Pro version.', 'msc-post-expiry' ); ?></p>
				<p>
					<a class="button button-primary msc-admin-button msc-admin-button-primary" href="https://anomalous.co.za" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html__( 'Learn More', 'msc-post-expiry' ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}
}
