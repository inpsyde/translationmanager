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

use Brain\Monkey\Actions;
use PHPUnit_Framework_MockObject_MockObject;
use Translationmanager\Module\Mlp\Integrator as Testee;
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
            $testee = $this->createTesteeInstance(['classExists']);
        }

        {
            $testee
                ->expects($this->exactly(2))
                ->method('classExists')
                ->withConsecutive(
                    ['Inpsyde\\MultilingualPress\\MultilingualPress'],
                    ['Multilingual_Press']
                )
                ->willReturnOnConsecutiveCalls(false, true);

            Actions\expectAdded('inpsyde_mlp_loaded')->once();
            Actions\expectAdded('multilingualpress.add_service_providers')->never();
        }

        {
            /** @var Testee $testee */
            $testee->integrate();
        }
    }

    /**
     * Test Mlp3 Integration
     */
    public function testMlp3Integration()
    {
        {
            $testee = $this->createTesteeInstance(['classExists']);
        }

        {
            $testee
                ->expects($this->once())
                ->method('classExists')
                ->with('Inpsyde\\MultilingualPress\\MultilingualPress')
                ->willReturn(true);

            Actions\expectAdded('inpsyde_mlp_loaded')->never();
            Actions\expectAdded('multilingualpress.add_service_providers')->once();
        }

        {
            /** @var Testee $testee */
            $testee->integrate();
        }
    }

    /**
     * Create Testee Instance
     *
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function createTesteeInstance(array $methods)
    {
        return $this
            ->getMockBuilder(Testee::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }
}
