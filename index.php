<?php
/*
 * Plugin Name: VK Screen Name Resolver
 * Plugin URI: https://myrotvorets.center/
 * Description: VK Screen Name Resolver
 * Version: 1.0.0
 * Author: Myrotvorets
 * Author URI: https://myrotvorets.center/
 * License: MIT
 * Domain Path: /lang
 */

use Myrotvorets\WordPress\VKScreenNameResolver\Plugin;

if ( defined( 'ABSPATH' ) ) {
	if ( defined( 'VENDOR_PATH' ) ) {
		/** @psalm-suppress UnresolvableInclude, MixedOperand */
		require_once constant( 'VENDOR_PATH' ) . '/vendor/autoload.php'; // NOSONAR
	} elseif ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	} elseif ( file_exists( ABSPATH . 'vendor/autoload.php' ) ) {
		/** @psalm-suppress UnresolvableInclude */
		require_once ABSPATH . 'vendor/autoload.php';
	}

	Plugin::instance();
}
