<?php
/**
 * Class Integrate
 *
 * @since   1.0.0
 * @package Translationmanager\Module\YoastSeo
 */

namespace Translationmanager\Module\YoastSeo;

use Translationmanager\Module\Integrable;

/**
 * Class Integrate
 *
 * @since   1.0.0
 * @package Translationmanager\Module\YoastSeo
 */
class Integrate implements Integrable {

	/**
	 * Plugin File
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin file path
	 */
	private $plugin_file;

	/**
	 * Integrate constructor
	 *
	 * @param string $plugin_file The plugin file path.
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct( $plugin_file ) {

		$this->plugin_file = $plugin_file;
	}

	/**
	 * @inheritdoc
	 */
	public function integrate() {

		$wordpress_seo = new \Translationmanager\Module\YoastSeo\WordPressSeo();

		add_action( 'translationmanager_outgoing_data', [
			$wordpress_seo,
			'prepare_outgoing',
		] );
		add_action(
			'translationmanager_updated_post',
			[ $wordpress_seo, 'update_translation', ],
			10,
			2
		);
	}
}
