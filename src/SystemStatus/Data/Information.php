<?php

namespace Translationmanager\SystemStatus\Data;

interface Information
{
    public function title(): string;

    public function collection(): array;
}
