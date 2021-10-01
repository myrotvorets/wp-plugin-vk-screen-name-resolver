<?php

namespace Myrotvorets\WordPress\VKScreenNameResolver;

use WildWolf\Utils\Singleton;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

final class REST_Controller {
	use Singleton;

	const NAMESPACE = 'vksnr/v1';

	private function __construct() {
		$this->register_routes();
	}

	private function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'resolve/(?P<name>[^\\/]+)',
			[
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => [ $this, 'permission_callback' ],
				'callback'            => [ $this, 'resolve_screen_name' ],
				'args'                => [
					'name' => [
						'required' => true,
						'type'     => 'string',
					],
				],
			],
		);
	}

	/**
	 * @return bool|WP_Error
	 */
	public function permission_callback() {
		if ( ! current_user_can( 'edit_criminals' ) ) {
			return new WP_Error(
				'rest_operation_not_allowed',
				__( 'You are not allowed to use this API.', 'vksnr' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}

		return true;
	}

	/**
	 * @return WP_REST_Response|WP_Error
	 */
	public function resolve_screen_name( WP_REST_Request $request ) {
		$name     = rawurlencode( (string) $request->get_param( 'name' ) );
		$token    = Settings::instance()->get_api_key();
		$url      = "https://api.vk.com/method/utils.resolveScreenName?screen_name={$name}&v=5.81&access_token={$token}";
		$response = wp_remote_get( $url, [ 'timeout' => 3 ] );  // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get
		$retval   = null;

		if ( ! is_wp_error( $response ) ) {
			$content = wp_remote_retrieve_body( $response );
			$status  = wp_remote_retrieve_response_code( $response );

			if ( 200 === $status ) {
				/** @var mixed */
				$data = json_decode( $content, true );
				if ( is_array( $data ) ) {
					if ( empty( $data['response'] ) ) {
						$retval = new WP_Error(
							'name_not_found',
							__( 'Name not found', 'vksnr' ),
							[ 'status' => 404 ]
						);
					} elseif ( isset( $data['error']['error_msg'] ) ) {
						$retval = new WP_Error(
							'bad_request',
							(string) $data['error']['error_msg'],
							[ 'status' => 400 ]
						);
					} elseif ( isset( $data['response']['object_id'] ) && isset( $data['response']['type'] ) ) {
						/** @psalm-var array{response: array{object_id: int, type: string}} $data */
						$retval = [
							'id'   => $data['response']['object_id'],
							'type' => $data['response']['type'],
						];
					}
				}
			}
		} else {
			$retval = $response;
		}

		if ( null === $retval ) {
			$retval = new WP_Error(
				'bad_upstream_response',
				__( 'Error communicating with the upstream server', 'vksnr' ),
				[ 'status' => 400 ]
			);
		}

		return rest_ensure_response( $retval );
	}
}
