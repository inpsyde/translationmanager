<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Integration\Module\WooCommerce;

use Brain\Monkey\Actions;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Module\WooCommerce\Bridge;
use Translationmanager\Module\WooCommerce\Integrator;
use TranslationmanagerTests\TestCase;

/**
 * Class IntegratorTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class IntegratorTest extends TestCase
{
    /**
     * Test Integration with WooCommerce Happens Correctly
     */
    public function testIntegration()
    {
        {
            Actions\expectAdded('translationmanager_outgoing_data')->once();
            Actions\expectAdded('translationmanager_updated_post')->once();
        }

        {
            Integrator::integrate(new ProcessorBusFactory(), '');
        }
    }
}
