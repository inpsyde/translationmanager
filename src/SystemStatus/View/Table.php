<?php

namespace Translationmanager\SystemStatus\View;

use Translationmanager\SystemStatus\Collection;

class Table implements Viewable
{
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function path()
    {
        // @todo This dirpath should be configurable.
        return dirname(__DIR__) . '/views/table.php';
    }
}
