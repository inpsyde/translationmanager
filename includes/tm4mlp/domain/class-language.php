<?php

namespace Tm4mlp\Domain;

/**
 * Represent a language as needed by TM.
 *
 * @package Tm4mlp\Domain
 */
class Language {
	protected $lang_code;
	protected $label;

	public function __construct( $lang_code, $label ) {

		$this->lang_code = $lang_code;
		$this->label     = $label;
	}

	/**
	 * @return mixed
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * @return mixed
	 */
	public function get_lang_code() {
		return $this->lang_code;
	}
}