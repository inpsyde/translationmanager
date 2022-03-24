<?php

namespace Translationmanager\SystemStatus\Item;

final class Item implements Informative
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $info;

    /**
     * @var string
     */
    private $shortDescription;

    /**
     * Item constructor
     *
     * @param string $name
     * @param string $info
     * @param string $shortDescription
     */
    public function __construct($name, $info, $shortDescription = '')
    {
        $this->name = $name;
        $this->info = $info;
        $this->shortDescription = $shortDescription;
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function info(): string
    {
        return $this->info;
    }

    /**
     * @inheritdoc
     */
    public function shortDescription(): string
    {
        return $this->shortDescription;
    }
}
