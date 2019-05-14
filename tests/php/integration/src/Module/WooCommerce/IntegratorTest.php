<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Integration\Module\WooCommerce;

use Brain\Monkey\Actions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Module\WooCommerce\Integrator;
use TranslationmanagerTests\TestCase;

/**
 * Class IntegratorTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class IntegratorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Test Integration with WooCommerce Happens Correctly
     */
    public function testIntegration()
    {
        $this->markTestSkipped('WooCommerce support is not FULLY available at the moment.');

        {
            Actions\expectAdded('translationmanager_outgoing_data')->once();
            Actions\expectAdded('translationmanager_updated_post')->once();
        }

        {
            Integrator::integrate(new ProcessorBusFactory(), '');
        }
    }
}
