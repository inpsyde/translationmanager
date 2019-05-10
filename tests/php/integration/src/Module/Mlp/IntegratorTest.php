<?php # -*- coding: utf-8 -*-

/*
 * This file is part of the Translation Manager package.
 *
 * (c) Guido Scialfa <dev@guidoscialfa.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TranslationmanagerTests\Integration\Module\Mlp;

use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use Translationmanager\Module\Mlp\Integrator as Testee;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use TranslationmanagerTests\TestCase;

/**
 * Class IntegratorTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class IntegratorTest extends TestCase
{
    /**
     * Test Mlp2 Integration
     */
    public function testMlp2Integration()
    {
        {
            Functions\when('get_file_data')->justReturn(['version' => '2']);

            Functions\when('Translationmanager\\Functions\\version_compare')
                ->justReturn(false);

            Actions\expectAdded('inpsyde_mlp_loaded')
                ->once();

            Actions\expectAdded('multilingualpress.add_service_providers')
                ->never();
        }

        {
            Testee::integrate(new ProcessorBusFactory(), 'file_plugin_path');
        }
    }

    /**
     * Test Mlp3 Integration
     */
    public function testMlp3Integration()
    {
        {
            Functions\when('get_file_data')->justReturn(['version' => '3']);

            Functions\when('Translationmanager\\Functions\\version_compare')
                ->justReturn(true);

            Actions\expectAdded('inpsyde_mlp_loaded')
                ->never();

            Actions\expectAdded('multilingualpress.add_service_providers')
                ->once();
        }

        {
            Testee::integrate(new ProcessorBusFactory(), 'file_plugin_path');
        }
    }
}
