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
	 * @inheritdoc
	 */
	public function integrate() {

		$wordpress_seo = new WordPressSeo();

		add_action( 'translationmanager_outgoing_data', [ $wordpress_seo, 'prepare_outgoing' ] );
		add_action( 'translationmanager_updated_post', [ $wordpress_seo, 'update_translation' ], 10, 2 );
	}
}
