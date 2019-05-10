<?php # -*- coding: utf-8 -*-

/*
 * This file is part of the Translation Manager package.
 *
 * (c) Guido Scialfa <dev@guidoscialfa.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TranslationmanagerTests\Unit\Module\Mlp;

use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use Mockery;
use Translationmanager\Module\Mlp\Integrator;
use TranslationmanagerTests\TestCase;

/**
 * Class IntegratorTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class IntegratorTest extends TestCase
{
    /**
     * Test Instance Creation
     */
    public function testInstance()
    {
        $testee = new Integrator('3.0.0');

        self::assertInstanceOf(Integrator::class, $testee);
    }

    /**
     * Test Mlp2 Integration
     */
    public function testMlp2Integration()
    {
        {
            $testee = new Integrator('3.0.0');
        }

        {
            Functions\when('Translationmanager\\Functions\\version_compare')
                ->justReturn(false);

            Actions\expectAdded('inpsyde_mlp_loaded')
                ->once();

            Actions\expectAdded('multilingualpress.add_service_providers')
                ->never();
        }

        {
            $testee->integrate();
        }
    }

    /**
     * Test Mlp3 Integration
     */
    public function testMlp3Integration()
    {
        {
            $testee = new Integrator('3.0.0');
        }

        {
            Functions\when('Translationmanager\\Functions\\version_compare')
                ->justReturn(true);

            Actions\expectAdded('inpsyde_mlp_loaded')
                ->never();

            Actions\expectAdded('multilingualpress.add_service_providers')
                ->once();
        }

        {
            $testee->integrate();
        }
    }
}
