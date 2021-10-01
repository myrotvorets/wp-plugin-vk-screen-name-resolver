<?php

namespace Myrotvorets\WordPress\VKScreenNameResolver;

use WildWolf\Utils\Singleton;

final class AdminSettings {
	use Singleton;

	const OPTION_GROUP = 'vksnr_settings';

	/** @var InputFactory */
	private $input_factory;

	/**
	 * Constructed during `admin_init`
	 */
	private function __construct() {
		$this->register_settings();
	}

	public function register_settings(): void {
		$this->input_factory = new InputFactory( Settings::OPTION_KEY, Settings::instance() );

		register_setting(
			self::OPTION_GROUP,
			Settings::OPTION_KEY,
			[
				'default'           => [],
				'sanitize_callback' => [ SettingsValidator::class, 'sanitize' ],
			]
		);

		$section = 'general-settings';
		add_settings_section(
			$section,
			'',
			'__return_empty_string',
			Admin::OPTIONS_MENU_SLUG
		);

		add_settings_field(
			'apikey',
			__( 'VK API Key', 'vksnr' ),
			[ $this->input_factory, 'input' ],
			Admin::OPTIONS_MENU_SLUG,
			$section,
			[
				'label_for'    => 'apikey',
				'type'         => 'password',
				'autocomplete' => 'off',
				'required'     => true,
			]
		);
	}
}
