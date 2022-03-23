<?php

namespace Translationmanager\SystemStatus\View;

use Closure;

class TemplateLoader
{
    private $viewable;

    public function __construct(Viewable $viewable)
    {
        $this->viewable = $viewable;
    }

    public function render()
    {
        $path = $this->viewable->path();
        $closure = Closure::bind(function () use ($path) {
            include $path;
        }, $this->viewable->collection());

        // Render.
        $closure();
    }
}
