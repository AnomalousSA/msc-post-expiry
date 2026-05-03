<?php
/**
 * Tests for uninstall cleanup.
 *
 * @package MSCPE\Tests
 */

namespace MSCPE\Tests;

require_once __DIR__ . '/class-mscpe-test-case.php';

/**
 * Test the uninstall.php cleanup logic.
 */
class Test_Uninstall extends MSCPE_Test_Case {

	/**
	 * Test uninstall.php file exists.
	 */
	public function test_uninstall_file_exists() {
		$this->assertFileExists( dirname( __DIR__ ) . '/uninstall.php' );
	}

	/**
	 * Test uninstall.php does not reference workflow cron hook.
	 */
	public function test_no_workflow_cron_reference() {
		$content = file_get_contents( dirname( __DIR__ ) . '/uninstall.php' );
		$this->assertStringNotContainsString( 'mscpe_process_workflow_steps', $content );
	}

	/**
	 * Test uninstall.php does not drop workflow tables.
	 */
	public function test_no_workflow_table_drops() {
		$content = file_get_contents( dirname( __DIR__ ) . '/uninstall.php' );
		$this->assertStringNotContainsString( 'mscpe_workflows', $content );
		$this->assertStringNotContainsString( 'mscpe_workflow_steps', $content );
	}

	/**
	 * Test uninstall.php contains expected option deletions.
	 */
	public function test_deletes_expected_options() {
		$content = file_get_contents( dirname( __DIR__ ) . '/uninstall.php' );
		$this->assertStringContainsString( "delete_option( 'mscpe_options' )", $content );
		$this->assertStringContainsString( "delete_option( 'mscpe_seo_options' )", $content );
		$this->assertStringContainsString( "delete_option( 'mscpe_rules' )", $content );
		$this->assertStringContainsString( "delete_option( 'mscpe_action_log' )", $content );
		$this->assertStringContainsString( "delete_option( 'mscpe_db_version' )", $content );
	}

	/**
	 * Test uninstall.php drops expected tables.
	 */
	public function test_drops_expected_tables() {
		$content = file_get_contents( dirname( __DIR__ ) . '/uninstall.php' );
		$this->assertStringContainsString( 'mscpe_analytics', $content );
		$this->assertStringContainsString( 'mscpe_rules', $content );
	}

	/**
	 * Test uninstall.php cleans up post meta.
	 */
	public function test_cleans_post_meta() {
		$content = file_get_contents( dirname( __DIR__ ) . '/uninstall.php' );
		$this->assertStringContainsString( 'mscpe_', $content );
		$this->assertStringContainsString( '_mscpe_', $content );
		$this->assertStringContainsString( 'DELETE FROM', $content );
	}

	/**
	 * Test uninstall.php has security check.
	 */
	public function test_has_security_check() {
		$content = file_get_contents( dirname( __DIR__ ) . '/uninstall.php' );
		$this->assertStringContainsString( 'WP_UNINSTALL_PLUGIN', $content );
		$this->assertStringContainsString( 'ABSPATH', $content );
	}

	/**
	 * Test uninstall.php cleans up cron hooks.
	 */
	public function test_cron_hooks_cleaned() {
		$content = file_get_contents( dirname( __DIR__ ) . '/uninstall.php' );
		$this->assertStringContainsString( 'mscpe_process_expired_posts', $content );
		$this->assertStringContainsString( 'mscpe_process_expiry_advanced', $content );
	}
}
