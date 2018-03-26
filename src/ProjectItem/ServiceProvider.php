<?php
/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\ProjectItem
 */

namespace Translationmanager\ProjectItem;

use Pimple\Container;
use Translationmanager\Service\BootstrappableServiceProvider;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\ProjectItem
 */
class ServiceProvider implements BootstrappableServiceProvider {

	/**
	 * @inheritdoc
	 */
	public function register( Container $c ) {

		$c['ProjectItem.PostType'] = function ( $c ) {

			return new PostType( $c['translationmanager.plugin'] );
		};
	}

	/**
	 * @inheritdoc
	 */
	public function boot( Container $c ) {

		add_action( 'init', [ $c['ProjectItem.PostType'], 'register_post_type' ] );
		add_filter(
			'translationmanager_project_item_row_actions',
			[ $c['ProjectItem.PostType'], 'filter_row_actions' ],
			10,
			2
		);
	}
}
