<?php

namespace Translationmanager\SystemStatus\View;

use Closure;

class TemplateLoader
{
    /**
     * @var Viewable
     */
    private $viewable;

    public function __construct(Viewable $viewable)
    {
        $this->viewable = $viewable;
    }

    public function render(): void
    {
        $path = $this->viewable->path();
        $closure = Closure::bind(function () use ($path) {
            /**
             * @psalm-suppress UnresolvableInclude
             */
            include $path;
        }, $this->viewable->collection());
        assert(is_callable($closure));

        // Render.
        $closure();
    }
}
