<?php

namespace Translationmanager\SystemStatus\Item;

interface Informative
{
    public function name();

    public function info();

    public function shortDescription();
}
