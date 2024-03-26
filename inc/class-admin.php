<?php

namespace Myrotvorets\WordPress\VKScreenNameResolver;

use WildWolf\Utils\Singleton;

final class Admin {
	use Singleton;

	const OPTIONS_MENU_SLUG = 'vksnr-settings';

	private function __construct() {
		$this->init();
	}

	private function init(): void {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'admin_init', [ AdminSettings::class, 'instance' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
	}

	public function admin_init(): void {
		if ( Settings::instance()->valid() ) {
			add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		}
	}

	public function admin_menu(): void {
		add_options_page( __( 'VK API Settings', 'vksnr' ), __( 'VK API', 'vksnr' ), 'manage_options', self::OPTIONS_MENU_SLUG, [ __CLASS__, 'options_page' ] );
	}

	public function add_meta_boxes( string $post_type ): void {
		if ( 'criminal' === $post_type ) {
			add_meta_box(
				'vk-screen-name-resolver',
				__( 'VK Screen Name Resolver', 'vksnr' ),
				[ __CLASS__, 'metabox_callback' ],
				$post_type,
				'normal'
			);
		}
	}

	public static function metabox_callback(): void {
		require __DIR__ . '/../views/metabox.php'; // NOSONAR
	}

	public static function options_page(): void {
		require __DIR__ . '/../views/options.php'; // NOSONAR
	}

	/**
	 * @param string $hook_suffix
	 * @psalm-suppress RiskyTruthyFalsyComparison
	 */
	public function admin_enqueue_scripts( $hook_suffix ): void {
		if (
			( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix )
			&& (
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				( ! empty( $_GET['post_type'] ) && 'criminal' === $_GET['post_type'] )
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				|| ( ! empty( $_GET['post'] ) && is_scalar( $_GET['post'] ) && 'criminal' === get_post_type( (int) $_GET['post'] ) )
			)
		) {
			wp_enqueue_script(
				'vk-screen-name-resolver',
				plugins_url( 'assets/vksnr.min.js', __DIR__ ),
				[ 'wp-api-fetch' ],
				(string) filemtime( __DIR__ . '/../assets/vksnr.min.js' ),
				true
			);
		}
	}
}
