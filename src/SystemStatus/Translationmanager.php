<?php
/**
 * Translation Manager Status
 *
 * @since   1.0.0
 * @package Translationmanager\SystemStatus
 */

namespace Translationmanager\SystemStatus;

use Inpsyde\SystemStatus\Data\Information;
use Inpsyde\SystemStatus\Item\Item;
use function Translationmanager\Functions\get_languages;
use function Translationmanager\Functions\translationmanager_api;
use Translationmanager\Plugin;

/**
 * Class Translationmanager
 *
 * @since   1.0.0
 * @package Translationmanager\SystemStatus
 */
class Translationmanager implements Information {
	/**
	 * The collection of information
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $collection = [];

	/**
	 * System Information Title
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Translationmanager constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->title = esc_html__( 'TranslationMANAGER', 'systemstatus' );
	}

	/**
	 * System Information Title
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function title() {

		return $this->title;
	}

	/**
	 * Collection
	 *
	 * @return array The collection of information
	 */
	public function collection() {

		return $this->collection;
	}

	/**
	 * Plugin Version
	 *
	 * @return void
	 */
	public function pluginVersion() {

		$this->collection['plugin_version'] = new Item(
			esc_html__( 'Plugin Version', 'systemstatus' ),
			Plugin::VERSION
		);
	}

	/**
	 * Api Connection Test
	 *
	 * @todo  Seems the api `info/whoami` returns null in case of success. Need fix.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function apiConnectionTest() {

		$test   = translationmanager_api()->get( 'info/whoami' );
		$status = true;

		if ( isset( $test['code'] ) && 403 === $test['code'] ) {
			$status = false;
		}

		$status = $status
			? esc_html__( 'Ok', 'translationmanager' )
			: sprintf(
				esc_html__( 'Connection failed with response %1$s:%2$s', 'translationmanager' ),
				'<strong>' . intval( $test['code'] ) . '</strong>',
				'<strong>' . esc_html( sanitize_text_field( $test['message'] ) ) . '</strong>'
			);

		$this->collection['api_connection'] = new Item(
			esc_html__( 'Api Connection', 'systemstatus' ),
			$status
		);
	}

	/**
	 * Activated Languages
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activatedLanguages() {

		$languages = get_languages();
		$lang_list = '';

		foreach ( $languages as $language ) {
			$lang_list .= $language->get_label() . ', ';
		}

		$lang_list = trim( $lang_list, ', ' );

		$this->collection['activated_languages'] = new Item(
			esc_html__( 'Activated Languages', 'translationmanager' ),
			$lang_list
		);
	}
}