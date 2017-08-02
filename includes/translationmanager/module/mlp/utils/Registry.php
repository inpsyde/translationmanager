<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Utils;

class Registry {

	/**
	 * @var array
	 */
	private $services = array();

	/**
	 * @return Image_Copier
	 */
	public function image_sync() {

		$this->services[ __FUNCTION__ ] or $this->services[ __FUNCTION__ ] = new Image_Copier();

		return $this->services[ __FUNCTION__ ];
	}

}