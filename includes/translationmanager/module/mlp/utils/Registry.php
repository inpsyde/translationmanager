<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Utils;

class Registry {

	/**
	 * @var array
	 */
	private $services = array();

	/**
	 * @param  \Mlp_Content_Relations $content_relations
	 *
	 * @return Image_Copier
	 */
	public function image_sync( \Mlp_Content_Relations $content_relations ) {

		$this->services[ __FUNCTION__ ] or $this->services[ __FUNCTION__ ] = [];
		$id = spl_object_hash( $content_relations );

		if ( ! array_key_exists( $id, $this->services[ __FUNCTION__ ] ) ) {
			$this->services[ __FUNCTION__ ][ $id ] = new Image_Copier( $content_relations );
		}

		return $this->services[ __FUNCTION__ ][ $id ];
	}

	/**
	 * @return Network_State
	 */
	public function network_state() {
		
		return Network_State::create();
	}

}