<?php # -*- coding: utf-8 -*-

/*
 * This file is part of the Translation Manager package.
 *
 * (c) Guido Scialfa <dev@guidoscialfa.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Translationmanager\Module\Mlp;

use Translationmanager\Module\Processor\ProcessorBusFactory;

/**
 * Class ConnectorFactory
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ConnectorFactory
{
    /**
     * @var ProcessorBusFactory
     */
    private $processorBusFactory;

    /**
     * ConnectorFactory constructor
     * @param ProcessorBusFactory $processorBusFactory
     */
    public function __construct(ProcessorBusFactory $processorBusFactory)
    {
        $this->processorBusFactory = $processorBusFactory;
    }

    /**
     * Create Instance of Connector
     * @param Adapter $adapter
     * @return Connector
     */
    public function create(Adapter $adapter)
    {
        $processorBus = $this->processorBusFactory->create();
        $processorBus
            ->pushProcessor(new Processor\PostDataBuilder())
            ->pushProcessor(new Processor\PostParentSync())
            ->pushProcessor(new Processor\PostSaver())
            ->pushProcessor(new Processor\PostThumbSync())
            ->pushProcessor(new Processor\TaxonomiesSync());

        return new Connector($adapter, $processorBus);
    }
}
