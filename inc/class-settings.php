<?php

namespace Myrotvorets\WordPress\VKScreenNameResolver;

use ArrayAccess;
use LogicException;
use WildWolf\Utils\Singleton;

/**
 * @psalm-type SettingsArray = array{
 *  apikey: string,
 * }
 *
 * @template-implements ArrayAccess<string, scalar>
 */
final class Settings implements ArrayAccess {
	use Singleton;

	/** @var string  */
	const OPTION_KEY = 'vksnr';

	/**
	 * @psalm-readonly
	 * @psalm-var SettingsArray
	 */
	private static $defaults = [
		'apikey' => '',
	];

	/**
	 * @var array
	 * @psalm-var SettingsArray
	 */
	private $options;

	/**
	 * @codeCoverageIgnore
	 */
	private function __construct() {
		$this->refresh();
	}

	public function refresh(): void {
		/** @var mixed */
		$settings      = get_option( self::OPTION_KEY );
		$this->options = SettingsValidator::ensure_data_shape( is_array( $settings ) ? $settings : [] );
	}

	/**
	 * @psalm-return SettingsArray
	 */
	public static function defaults(): array {
		return self::$defaults;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetExists( $offset ): bool {
		return isset( $this->options[ (string) $offset ] );
	}

	/**
	 * @param mixed $offset
	 * @return int|string|bool|null
	 */
	public function offsetGet( $offset ): mixed {
		return $this->options[ (string) $offset ] ?? null;
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @psalm-return never
	 * @throws LogicException
	 */
	public function offsetSet( $offset, $value ): void {
		throw new LogicException();
	}

	/**
	 * @param mixed $offset
	 * @psalm-return never
	 * @throws LogicException
	 */
	public function offsetUnset( $offset ): void {
		throw new LogicException();
	}

	public function valid(): bool {
		return ! empty( $this->options['apikey'] );
	}

	public function get_api_key(): string {
		return $this->options['apikey'];
	}
}
