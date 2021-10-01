<?php

namespace Myrotvorets\WordPress\VKScreenNameResolver;

use WildWolf\Utils\Singleton;

final class Plugin {
	use Singleton;

	private function __construct() {
		if ( is_admin() ) {
			add_action( 'init', [ Admin::class, 'instance' ] );
		} else {
			add_action( 'rest_api_init', [ REST_Controller::class, 'instance' ] );
		}
	}
}
