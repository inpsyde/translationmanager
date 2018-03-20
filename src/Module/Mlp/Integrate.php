<?php
/**
 * Class Integrate
 *
 * @since   1.0.0
 * @package Translationmanager\Module\Mlp
 */

namespace Translationmanager\Module\Mlp;

use Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection;
use Translationmanager\Module\Integrable;
use Translationmanager\Module\Mlp;

/**
 * Class Integrate
 *
 * @since   1.0.0
 * @package Translationmanager\Module\Mlp
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
	 * @since 1.0.0
	 *
	 * @param string $plugin_file The plugin file path.
	 */
	public function __construct( $plugin_file ) {

		$this->plugin_file = $plugin_file;
	}

	/**
	 * @inheritdoc
	 */
	public function integrate() {

		// Check for version.
		$plugin_data = get_file_data( $this->plugin_file, [
			'version' => 'Version',
		] );

		( - 1 === version_compare( '2.0.0', $plugin_data['version'] ) )
			? $this->mlp3()
			: $this->mlp2();
	}

	/**
	 * Mlp 2 Integration
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function mlp2() {

		$plugin_file = $this->plugin_file;

		add_action(
			'inpsyde_mlp_loaded',
			function ( \Inpsyde_Property_List_Interface $data ) use ( $plugin_file ) {

				self::action(
					new Adapter(
						$plugin_file,
						$data->get( 'site_relations' ),
						$data->get( 'content_relations' )
					)
				);
			}
		);
	}

	/**
	 * Mlp 3 Integration
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function mlp3() {

		add_action(
			'multilingualpress.add_service_providers',
			function ( ServiceProvidersCollection $providers ) {

				$providers->add( new ServiceProvider() );
			}
		);
	}

	/**
	 * Action
	 *
	 * Actually the implementation for the Module.
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Module\Mlp\Adapter $adapter The instance of the adapter.
	 */
	public static function action( Adapter $adapter ) {

		$connector = new Connector( $adapter );

		// TM interface hooks to let it know about the environment.
		add_filter( 'translationmanager_current_language', [ $connector, 'current_language' ] );
		add_filter( 'translationmanager_languages', [ $connector, 'related_sites' ], 10, 2 );
		add_filter( 'translation_manager_languages_by_site_id', [ $connector, 'related_sites' ], 10, 2 );

		// Setup the translation workflow.
		add_action( 'translationmanager_outgoing_data', [ $connector, 'prepare_outgoing' ] );
		add_filter( 'translationmanager_post_updater', [ $connector, 'prepare_updater' ] );
	}
}
