<?php

namespace Translationmanager\SystemStatus;

use Translationmanager\SystemStatus\Data\Information;
use Translationmanager\SystemStatus\View\TemplateLoader;

class Builder
{
    const FILTER_DATA_METHODS = 'system_status.data_methods';

    private $list;

    private $viewableName;

    public function __construct(array $list, $viewableName)
    {
        $this->list = $list;
        $this->viewableName = $viewableName;
    }

    public function build()
    {
        return new TemplateLoader(
            $this->viewable(
                $this->buildTheCollection()
            )
        );
    }

    private function buildTheCollection()
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
            $dataList[$key] = $data;

            $this->initializeData($data);
        }

        return new Collection($dataList);
    }

    private function initializeData(Information $data)
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
                $methods = array_diff($methods, array_filter((array)$interfaceMethods));
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
         */
        $methods = apply_filters(self::FILTER_DATA_METHODS, $methods, $data, $interfaces);

        // Set up the internal data collection.
        foreach ($methods as $method) {
            $data->$method();
        }
    }

    private function viewable(Collection $collection)
    {
        $className = __NAMESPACE__ . '\\View\\' . $this->normalizeClassName($this->viewableName);

        if (! class_exists($className)) {
            throw new SystemStatusException(
                sprintf('View class %s doesn\'t exists.', $className)
            );
        }

        return new $className($collection);
    }

    private function sanitizeKey($item)
    {
        $key = strtolower($item);
        $key = preg_replace('/[^a-z0-9_\-]/', '', $key);

        return $key;
    }

    private function normalizeClassName($key)
    {
        return preg_replace('/\s/', '', ucwords(str_replace(['_', '-'], ' ', $key)));
    }
}
