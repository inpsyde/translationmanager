<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\stubs;

use PHPUnit\Framework\TestCase;
use Translationmanager\Module\Integrable;
use Translationmanager\Module\Processor\ProcessorBusFactory;

/**
 * Class IntegratorStub
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class IntegratorStub extends TestCase implements Integrable
{
    public function integrate()
    {
    }
}
