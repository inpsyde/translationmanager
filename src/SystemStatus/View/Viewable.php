<?php

namespace Translationmanager\SystemStatus\View;

use Translationmanager\SystemStatus\Collection;

interface Viewable
{
    public function collection(): Collection;

    public function path(): string;
}
