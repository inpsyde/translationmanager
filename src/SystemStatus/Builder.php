<?php

namespace Translationmanager\SystemStatus;

use Translationmanager\SystemStatus\Data\Information;
use Translationmanager\SystemStatus\View\TemplateLoader;
use Translationmanager\SystemStatus\View\Viewable;

class Builder
{
    public const FILTER_DATA_METHODS = 'system_status.data_methods';

    /**
     * @var array
     */
    private $list;

    /**
     * @var string
     */
    private $viewableName;

    public function __construct(array $list, string $viewableName)
    {
        $this->list = $list;
        $this->viewableName = $viewableName;
    }

    public function build(): TemplateLoader
    {
        return new TemplateLoader(
            $this->viewable(
                $this->buildTheCollection()
            )
        );
    }

    private function buildTheCollection(): Collection
    {
        $dataList = [];

        foreach ($this->list as $item) {
            $className = $this->normalizeClassName($item);

            if (! class_exists($className)) {
                throw new SystemStatusException(
                    sprintf('Data class %s not available.', $className)
                );
            }

            $key = $this->sanitizeKey($item);
            $data = new $className();
            assert($data instanceof Information);
            $dataList[$key] = $data;

            $this->initializeData($data);
        }

        return new Collection($dataList);
    }

    private function initializeData(Information $data): void
    {
        $methods = get_class_methods($data);

        if (! $methods) {
            return;
        }

        // Remove the interface methods. We need to call only data methods.
        $interfaces = class_implements($data);

        if ($interfaces) {
            foreach ($interfaces as $interface) {
                $interfaceMethods = get_class_methods($interface);
                $methods = array_diff($methods, array_filter($interfaceMethods));
            }
        }

        // Remove magic methods.
        $methods = array_diff($methods, [
            '__construct',
            '__destruct',
            '__call',
            '__callStatic',
            '__get',
            '__set',
            '__isset',
            '__unset',
            '__sleep',
            '__wakeup',
            '__toString',
            '__invoke',
            '__set_state',
            '__clone',
            '__debugInfo',
        ]);

        /**
         * Data Methods Filter
         *
         * @param array $methods The methods to call to set the data for the collection.
         * @param Information $data Information class from which the methods should be called.
         * @param array $interfaces The interfaces implemented by the $data instance.
         *
         * @psalm-suppress TooManyArguments
         */
        $methods = apply_filters(self::FILTER_DATA_METHODS, $methods, $data, $interfaces);

        // Set up the internal data collection.
        foreach ($methods as $method) {
            $data->$method();
        }
    }

    private function viewable(Collection $collection): Viewable
    {
        $className = __NAMESPACE__ . '\\View\\' . $this->normalizeClassName($this->viewableName);

        if (! class_exists($className)) {
            throw new SystemStatusException(
                sprintf('View class %s doesn\'t exists.', $className)
            );
        }

        $obj = new $className($collection);
        assert($obj instanceof Viewable);
        return $obj;
    }

    private function sanitizeKey($item): string
    {
        $key = strtolower($item);
        $key = (string) preg_replace('/[^a-z0-9_\-]/', '', $key);

        return $key;
    }

    private function normalizeClassName(string $key): string
    {
        return (string) preg_replace('/\s/', '', ucwords(str_replace(['_', '-'], ' ', $key)));
    }
}
