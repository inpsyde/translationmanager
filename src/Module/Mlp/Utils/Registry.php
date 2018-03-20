<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Utils;

use Translationmanager\Module\Mlp\Adapter;

class Registry {

	/**
	 * @var array
	 */
	private $services = [];

	/**
	 * @param  \Mlp_Content_Relations $content_relations
	 *
	 * @return ImageCopier
	 */
	public function image_sync( Adapter $adapter ) {

		$this->services[ __FUNCTION__ ] or $this->services[ __FUNCTION__ ] = [];
		$id = spl_object_hash( $adapter );

		if ( ! array_key_exists( $id, $this->services[ __FUNCTION__ ] ) ) {
			$this->services[ __FUNCTION__ ][ $id ] = new ImageCopier( $adapter );
		}

		return $this->services[ __FUNCTION__ ][ $id ];
	}

	/**
	 * @return NetworkState
	 */
	public function network_state() {

		return NetworkState::create();
	}
}
