<?php

namespace Translationmanager\SystemStatus\View;

use Translationmanager\SystemStatus\Collection;

class Table implements Viewable
{
    /**
     * @var Collection
     */
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function collection(): Collection
    {
        return $this->collection;
    }

    public function path(): string
    {
        // @todo This dirpath should be configurable.
        return dirname(__DIR__) . '/views/table.php';
    }
}
