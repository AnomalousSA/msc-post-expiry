<?php
/**
 * Tests for helper functions.
 *
 * @package MSCPE\Tests
 */

namespace MSCPE\Tests;

require_once __DIR__ . '/class-mscpe-test-case.php';

// Now load the functions file (skipping class includes since the bootstrap already defines constants).
// We need to include just the function definitions.
// The main file has exit guards + class includes, so we source the functions directly.

/**
 * Test the plugin's helper functions.
 */
class Test_Helper_Functions extends MSCPE_Test_Case {

	/**
	 * Load helper functions once.
	 */
	public static function set_up_before_class() {
		parent::set_up_before_class();

		// Source the helper functions from the main plugin file.
		// They are guarded by function_exists, so safe to re-declare.
		$file    = dirname( __DIR__ ) . '/msc-post-expiry.php';
		$content = file_get_contents( $file );

		// Extract only function blocks.
		preg_match_all( '/if\s*\(\s*!\s*function_exists\s*\(\s*\'(mscpe_\w+)\'\s*\)\s*\)\s*\{(.+?)\n\}/s', $content, $matches );

		foreach ( $matches[1] as $i => $fn_name ) {
			if ( ! function_exists( $fn_name ) ) {
				// The function is inside an if block, eval it safely.
				// phpcs:ignore Squiz.PHP.Eval.Discouraged -- test-only
				eval( $matches[2][ $i ] );
			}
		}
	}

	/**
	 * Test mscpe_get_expiry_datetime returns false when no meta set.
	 */
	public function test_get_expiry_datetime_no_meta() {
		$result = mscpe_get_expiry_datetime( 1 );
		$this->assertFalse( $result );
	}

	/**
	 * Test mscpe_get_expiry_datetime returns array when meta is set.
	 */
	public function test_get_expiry_datetime_with_meta() {
		$this->set_post_meta( 42, 'mscpe_expiry_date', '2026-06-15' );
		$this->set_post_meta( 42, 'mscpe_expiry_time', '14:30' );

		$result = mscpe_get_expiry_datetime( 42 );
		$this->assertIsArray( $result );
		$this->assertSame( '2026-06-15', $result['date'] );
		$this->assertSame( '14:30', $result['time'] );
	}

	/**
	 * Test mscpe_get_expiry_datetime defaults time to 00:00.
	 */
	public function test_get_expiry_datetime_default_time() {
		$this->set_post_meta( 42, 'mscpe_expiry_date', '2026-06-15' );

		$result = mscpe_get_expiry_datetime( 42 );
		$this->assertIsArray( $result );
		$this->assertSame( '00:00', $result['time'] );
	}

	/**
	 * Test mscpe_is_post_expired returns false for no expiry.
	 */
	public function test_is_post_expired_no_expiry() {
		$this->assertFalse( mscpe_is_post_expired( 1 ) );
	}

	/**
	 * Test mscpe_is_post_expired returns true for past date.
	 */
	public function test_is_post_expired_past() {
		$this->set_post_meta( 42, 'mscpe_expiry_date', '2020-01-01' );
		$this->set_post_meta( 42, 'mscpe_expiry_time', '00:00' );

		$this->assertTrue( mscpe_is_post_expired( 42 ) );
	}

	/**
	 * Test mscpe_is_post_expired returns false for future date.
	 */
	public function test_is_post_expired_future() {
		$this->set_post_meta( 42, 'mscpe_expiry_date', '2099-12-31' );
		$this->set_post_meta( 42, 'mscpe_expiry_time', '23:59' );

		$this->assertFalse( mscpe_is_post_expired( 42 ) );
	}

	/**
	 * Test mscpe_get_expiry_status returns "No expiry set" when no meta.
	 */
	public function test_get_expiry_status_no_expiry() {
		$status = mscpe_get_expiry_status( 1 );
		$this->assertSame( 'No expiry set', $status );
	}

	/**
	 * Test mscpe_get_expiry_status returns "Expired" for past date.
	 */
	public function test_get_expiry_status_expired() {
		$this->set_post_meta( 42, 'mscpe_expiry_date', '2020-01-01' );
		$this->set_post_meta( 42, 'mscpe_expiry_time', '00:00' );

		$status = mscpe_get_expiry_status( 42 );
		$this->assertSame( 'Expired', $status );
	}

	/**
	 * Test mscpe_get_expiry_status returns remaining time for future date.
	 */
	public function test_get_expiry_status_future() {
		$future = gmdate( 'Y-m-d', strtotime( '+10 days' ) );
		$this->set_post_meta( 42, 'mscpe_expiry_date', $future );
		$this->set_post_meta( 42, 'mscpe_expiry_time', '23:59' );

		$status = mscpe_get_expiry_status( 42 );
		$this->assertStringContainsString( 'remaining', $status );
	}
}
