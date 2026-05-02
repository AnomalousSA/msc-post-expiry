<?php
/**
 * SEO handling for expired posts.
 *
 * Adds noindex, nofollow, and canonical meta tags to expired posts.
 *
 * @package MSCPE
 */

namespace MSCPE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles SEO meta tags for expired posts.
 */
class SEO {

	/**
	 * Meta key for SEO noindex.
	 */
	const META_KEY_NOINDEX = '_mscpe_seo_noindex';

	/**
	 * Meta key for SEO nofollow.
	 */
	const META_KEY_NOFOLLOW = '_mscpe_seo_nofollow';

	/**
	 * Meta key for SEO canonical URL.
	 */
	const META_KEY_CANONICAL = '_mscpe_seo_canonical';

	/**
	 * Meta key for SEO status code.
	 */
	const META_KEY_STATUS_CODE = '_mscpe_seo_status_code';

	/**
	 * Option key for global SEO settings.
	 */
	const OPTION_KEY = 'mscpe_seo_options';

	/**
	 * Default SEO options.
	 *
	 * @var array<string,mixed>
	 */
	private static $default_options = array(
		'noindex_enabled'      => 1,
		'nofollow_enabled'     => 1,
		'canonical_strategy'   => 'category',
		'custom_canonical_url' => '',
	);

	/**
	 * Main plugin instance.
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin Main plugin instance.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'wp_head', array( $this, 'output_seo_meta' ), 1 );
	}

	/**
	 * Gets default SEO options.
	 *
	 * @return array<string,mixed>
	 */
	public static function get_default_options() {
		return self::$default_options;
	}

	/**
	 * Gets current SEO options.
	 *
	 * @return array<string,mixed>
	 */
	public function get_options() {
		$options = get_option( self::OPTION_KEY, array() );
		return wp_parse_args( $options, self::$default_options );
	}

	/**
	 * Saves SEO options.
	 *
	 * @param array<string,mixed> $options Options to save.
	 * @return void
	 */
	public function save_options( $options ) {
		$defaults   = self::$default_options;
		$clean      = array();
		$clean_keys = array( 'noindex_enabled', 'nofollow_enabled', 'canonical_strategy', 'custom_canonical_url' );

		foreach ( $clean_keys as $key ) {
			if ( isset( $options[ $key ] ) ) {
				switch ( $key ) {
					case 'noindex_enabled':
					case 'nofollow_enabled':
						$clean[ $key ] = ! empty( $options[ $key ] ) ? 1 : 0;
						break;
					case 'canonical_strategy':
						$allowed       = array( 'category', 'homepage', 'custom' );
						$clean[ $key ] = in_array( $options[ $key ], $allowed, true ) ? $options[ $key ] : $defaults['canonical_strategy'];
						break;
					case 'custom_canonical_url':
						$clean[ $key ] = esc_url_raw( $options[ $key ] );
						break;
					default:
						$clean[ $key ] = $options[ $key ];
				}
			} else {
				$clean[ $key ] = $defaults[ $key ];
			}
		}

		update_option( self::OPTION_KEY, $clean );
	}

	/**
	 * Checks if a post is marked as expired (processed).
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public function is_post_expired( $post_id ) {
		return (bool) get_post_meta( $post_id, '_mscpe_expiry_processed', true );
	}

	/**
	 * Outputs SEO meta tags in wp_head.
	 *
	 * @return void
	 */
	public function output_seo_meta() {
		if ( ! is_singular() ) {
			return;
		}

		$post = get_post();
		if ( ! $post ) {
			return;
		}

		if ( ! $this->is_post_expired( $post->ID ) ) {
			return;
		}

		$options = $this->get_options();

		// Check per-post overrides first.
		$noindex  = (int) get_post_meta( $post->ID, self::META_KEY_NOINDEX, true );
		$nofollow = (int) get_post_meta( $post->ID, self::META_KEY_NOFOLLOW, true );

		// Fall back to global settings if not set.
		if ( -1 === $noindex ) {
			return;
		}
		$noindex = ( 1 === $noindex || ( 0 === $noindex && $options['noindex_enabled'] ) );

		if ( -1 === $nofollow ) {
			return;
		}
		$nofollow = ( 1 === $nofollow || ( 0 === $nofollow && $options['nofollow_enabled'] ) );

		if ( ! $noindex && ! $nofollow ) {
			return;
		}

		echo '<meta name="robots" content="' . esc_attr( $this->build_robots_content( $noindex, $nofollow ) ) . '" />' . "\n";

		// Output canonical URL.
		$canonical = $this->get_canonical_url( $post );
		if ( $canonical ) {
			echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
		}
	}

	/**
	 * Builds the robots meta content value.
	 *
	 * @param bool $noindex  Should add noindex.
	 * @param bool $nofollow Should add nofollow.
	 * @return string
	 */
	private function build_robots_content( $noindex, $nofollow ) {
		$parts = array();
		if ( $noindex ) {
			$parts[] = 'noindex';
		}
		if ( $nofollow ) {
			$parts[] = 'nofollow';
		}
		return implode( ',', $parts );
	}

	/**
	 * Gets the canonical URL for an expired post.
	 *
	 * @param \WP_Post $post Post object.
	 * @return string|null
	 */
	public function get_canonical_url( $post ) {
		// Check per-post custom canonical first.
		$custom_canonical = (string) get_post_meta( $post->ID, self::META_KEY_CANONICAL, true );
		if ( ! empty( $custom_canonical ) ) {
			return $custom_canonical;
		}

		$options = $this->get_options();

		switch ( $options['canonical_strategy'] ) {
			case 'homepage':
				return home_url( '/' );

			case 'custom':
				if ( ! empty( $options['custom_canonical_url'] ) ) {
					return $options['custom_canonical_url'];
				}
				return null;

			case 'category':
			default:
				$terms = get_the_terms( $post->ID, 'category' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$category = reset( $terms );
					return get_term_link( $category );
				}
				return home_url( '/' );
		}
	}

	/**
	 * Sets SEO meta for a post upon expiry.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $action  Expiry action.
	 * @return void
	 */
	public function apply_seo_on_expiry( $post_id, $action = 'expired' ) {
		$options = $this->get_options();

		if ( ! $options['noindex_enabled'] && ! $options['nofollow_enabled'] ) {
			return;
		}

		if ( $options['noindex_enabled'] ) {
			update_post_meta( $post_id, self::META_KEY_NOINDEX, 1 );
		}

		if ( $options['nofollow_enabled'] ) {
			update_post_meta( $post_id, self::META_KEY_NOFOLLOW, 1 );
		}

		// Set canonical based on strategy.
		$canonical_url = '';
		switch ( $options['canonical_strategy'] ) {
			case 'category':
				$terms = get_the_terms( $post_id, 'category' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$category      = reset( $terms );
					$canonical_url = get_term_link( $category );
				}
				break;

			case 'homepage':
				$canonical_url = home_url( '/' );
				break;

			case 'custom':
				$canonical_url = $options['custom_canonical_url'];
				break;
		}

		if ( ! empty( $canonical_url ) && ! is_wp_error( $canonical_url ) ) {
			update_post_meta( $post_id, self::META_KEY_CANONICAL, $canonical_url );
		}
	}

	/**
	 * Clears SEO meta for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function clear_seo_meta( $post_id ) {
		delete_post_meta( $post_id, self::META_KEY_NOINDEX );
		delete_post_meta( $post_id, self::META_KEY_NOFOLLOW );
		delete_post_meta( $post_id, self::META_KEY_CANONICAL );
		delete_post_meta( $post_id, self::META_KEY_STATUS_CODE );
	}
}
