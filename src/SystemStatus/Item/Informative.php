<?php

namespace Translationmanager\SystemStatus\Item;

interface Informative
{
    public function name(): string;

    public function info(): string;

    public function shortDescription(): string;
}
