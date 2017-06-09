<?php

namespace Tmwp\Module;

use WPSEO_Meta;

class WordPress_Seo {
	/**
	 * Append data from SEO plugin.
	 *
	 * @param array    $current
	 * @param \WP_Post $source
	 *
	 * @return mixed
	 */
	public function prepare_outgoing( $current, $source ) {
		$current['wordpress_seo'] = array();

		foreach ( $this->get_meta_fields() as $internal => $meta_field ) {
			$current['wordpress_seo'][ $internal ] = get_post_meta( $source->ID, $meta_field, true );
		}

		return $current;
	}

	/**
	 * Gather meta field names.
	 *
	 * @see WPSEO_Meta::$meta_fields
	 */
	protected function get_meta_fields() {
		return array(
			'snippetpreview' => WPSEO_Meta::$meta_prefix . 'snippetpreview',
			'title'          => WPSEO_Meta::$meta_prefix . 'title',
			'metadesc'       => WPSEO_Meta::$meta_prefix . 'metadesc',
			'metakeywords'   => WPSEO_Meta::$meta_prefix . 'metakeywords',
			'bctitle'        => WPSEO_Meta::$meta_prefix . 'bctitle',
			'canonical'      => WPSEO_Meta::$meta_prefix . 'canonical'
		);
	}
}