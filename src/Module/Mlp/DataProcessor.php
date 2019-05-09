<?php // -*- coding: utf-8 -*-

/*
 * This file is part of the Translation Manager package.
 *
 * (c) Guido Scialfa <dev@guidoscialfa.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Translationmanager\Module\Mlp;

use Translationmanager\Module\DataProcessable;
use Translationmanager\TranslationData;
use WP_Post;

/**
 * Class DataProcessor
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class DataProcessor implements DataProcessable
{
    /**
     * @var \Translationmanager\Module\Mlp\ProcessorBus
     */
    private $processorBus;

    /**
     * DataProcessor constructor
     *
     * @param \Translationmanager\Module\Mlp\ProcessorBus $processorBus
     */
    public function __construct(ProcessorBus $processorBus)
    {
        $this->processorBus = $processorBus;
    }

    /**
     * @inheritDoc
     */
    public function prepare_outgoing(TranslationData $data)
    {
        // TODO: Implement prepare_outgoing() method.
    }

    /**
     * @inheritDoc
     */
    public function update_translation(WP_Post $post, TranslationData $data)
    {
        // TODO: Implement update_translation() method.
    }
}
