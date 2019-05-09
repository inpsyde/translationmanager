<?php // -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Utils;

use Translationmanager\Module\Mlp\Adapter;

class Registry
{
    /**
     * @var array
     */
    private $services = [];

    /**
     * @param \Translationmanager\Module\Mlp\Adapter $adapter
     *
     * @return ImageCopier
     */
    public function image_sync(Adapter $adapter)
    {
        if (!isset($this->services[__FUNCTION__])) {
            $this->services[__FUNCTION__] = [];
        }

        $id = spl_object_hash($adapter);

        if (!array_key_exists($id, $this->services[__FUNCTION__])) {
            $this->services[__FUNCTION__][$id] = new ImageCopier($adapter);
        }

        return $this->services[__FUNCTION__][$id];
    }

    /**
     * @return NetworkState
     */
    public function network_state()
    {
        return NetworkState::create();
    }
}
