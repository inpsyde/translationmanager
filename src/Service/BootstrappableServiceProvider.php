<?php
/**
 *
 * @since   1.0.0
 * @package Translationmanager\Service
 */

namespace Translationmanager\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class BootstrappableServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Service
 */
interface BootstrappableServiceProvider extends ServiceProviderInterface {
	public function boot( Container $container );
}
