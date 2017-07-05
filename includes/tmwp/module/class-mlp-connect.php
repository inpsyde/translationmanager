<?php # -*- coding: utf-8 -*-
/**
 * Bridge between the translation data and the MLP API
 *
 * @version 2017.03.01
 * @author  toscho
 */

namespace Tmwp\Module;

use Tmwp\Domain\Language;
use Tmwp\Translation_Data;

class Mlp_Connect {

	const TMWP_MLP_UPDATED_POST = 'tmwp_mlp_module_updated_post';
	const UPDATED_BY_MLP = 'updated-by-multilingualpress';

	/**
	 * @var \Mlp_Site_Relations
	 */
	private $site_relations;

	/**
	 * @var \Mlp_Content_Relations
	 */
	private $content_relations;

	/**
	 * @param \Mlp_Site_Relations    $site_relations
	 * @param \Mlp_Content_Relations $content_relations
	 */
	public function __construct( \Mlp_Site_Relations $site_relations, \Mlp_Content_Relations $content_relations ) {

		$this->site_relations    = $site_relations;
		$this->content_relations = $content_relations;
	}

	/**
	 * @wp-hook tmwp_outgoing_data
	 *
	 * @param Translation_Data $data
	 */
	public function prepare_outgoing( Translation_Data $data ) {

		/**
		 * Fires before data it is sent to API.
		 * Allow third parties to edit it being aware of MLP API objects.
		 *
		 * @param Translation_Data       $data              Translation data
		 * @param \Mlp_Site_Relations    $site_relations    MLP site relations API
		 * @param \Mlp_Content_Relations $content_relations MLP content relations API
		 */
		$outgoing_data = do_action(
			'tmwp_mlp_module_outgoing_post',
			$data,
			$this->site_relations,
			$this->content_relations
		);

		return $outgoing_data;
	}

	/**
	 * @wp-hook tmwp_post_updater
	 *          
	 * @return callable
	 */
	public function prepare_updater() {

		return array( $this, 'update_translations' );
	}

	/**
	 * @param Translation_Data $data
	 *
	 * @return null|\WP_Post
	 */
	public function update_translations( Translation_Data $data ) {

		__1__SANITY_CHECK : {

			if ( ! $data->is_valid() ) {
				return null;
			}
		}

		__2__STORE_BASIC_INFO___________________________________________________________________ : {

			$source_site_id = $data->source_post_id();

			$target_site_id = $data->target_site_id();

			$post_vars = get_object_vars( new \WP_Post( new \stdClass() ) );

			// Let's extract only post data from received translation data
			$translated_data = array();
			foreach ( $post_vars as $key ) {
				if ( $data->has_value( $key ) ) {
					$translated_data[ $key ] = $data->get_value( $key );
				}
			}
		}

		__3__GET_SOURCE_POST_OR_FAIL____________________________________________________________ : {

			switch_to_blog( $source_site_id );
			$source_post = get_post( $data->source_post_id() );
			restore_current_blog();

			if ( ! $source_post ) {
				// TODO Error handling, payed for nothing.
				return null;
			}
		}

		__4__CHECK_IF_SOURCE_POST_HAS_A_TRANSLATION_ALREADY_____________________________________ : {

			/** @var array $linked_posts Array with site ID as keys and content ID as values. */
			$linked_posts     = $this->content_relations->get_relations( $source_site_id, $source_post->ID );
			$linked_post      = array_key_exists( $target_site_id, $linked_posts )
				? get_post( $linked_posts[ $target_site_id ] )
				: null;
			$linked_post_data = $linked_post ? $linked_post->to_array() : array();
		}

		__5__BUILD_POST_DATA_INFO_WITH_EVERYTHING_WE_KNOW_______________________________________ : {

			$post_data = array_merge( $source_post->to_array(), $linked_post_data, $translated_data );

			/*
			 * Among following properties only ID should be passed, and we will force it to be existing linked post,
			 * if exists, otherwise we ensure is empty to enforce creation.
			 */
			unset(
				$post_data[ 'ID' ],
				$post_data[ 'guid' ],
				$post_data[ 'post_modified' ],
				$post_data[ 'post_modified_gmt' ]
			);

			$linked_post and $post_data[ 'ID' ] = $linked_post->ID;
		}


		__6__HANDLE_POST_PARENT_________________________________________________________________ : {

			$post_data[ 'post_parent' ] = $linked_post ? $linked_post->post_parent : 0;

			/*
			 * If source post has a parent post, we want to set a parent to target post as well, so we check if there's
			 * a connection on this site for the source post post parent
			 */
			if ( $source_post->post_parent && ! $linked_post ) {

				$linked_parents = $this->content_relations->get_relations( $source_site_id, $source_post->post_parent );
				if ( array_key_exists( $target_site_id, $linked_parents ) ) {
					$post_data[ 'post_parent' ] = $linked_parents[ $target_site_id ];
				}
			}
		}

		__7__SAVE_TARGET_POST_OR_FAIL__________________________________________________________ : {

			switch_to_blog( $target_site_id );

			$target_post_id = wp_update_post( $post_data );

			if (
				! $target_post_id
				|| is_wp_error( $target_post_id )
				|| ! ( $target_post = get_post( $target_post_id ) )
			) {

				restore_current_blog();

				// TODO Error handling, payed for nothing.

				return null;
			}
		}

		__8__LINK_SOURCE_AND_TARGET_POSTS_IF_NOT_LINKED_ALREADY_________________________________ : {

			if ( ! $linked_post ) {
				$this->content_relations->set_relation(
					$source_site_id,
					$target_site_id,
					$source_post->ID,
					$target_post->ID
				);
			}
		}

		__9__RESTORE_BLOG_AND_RETURN_UPDATED_POST_______________________________________________ : {

			restore_current_blog();

			$data->set_meta( self::UPDATED_BY_MLP, true );

			return $target_post;
		}
	}

	/**
	 * @wp-hook tmwp_updated_post
	 *
	 * @param \WP_Post         $translated_post
	 * @param Translation_Data $data
	 */
	public function notify_third_party( \WP_Post $translated_post, Translation_Data $data ) {

		if ( ! $data->get_meta( self::UPDATED_BY_MLP ) ) {
			return;
		}

		/**
		 * Fires after a translation post is updated, giving other modules opportunity to edit/use
		 * the just translated post also accessing translation data received from the API.
		 *
		 * Note: runs in the context of `$target_post` site.
		 *
		 * @param \WP_Post               $translated_post   The just-updated post
		 * @param Translation_Data       $data              Translation data
		 * @param \Mlp_Site_Relations    $site_relations    MLP site relations API
		 * @param \Mlp_Content_Relations $content_relations MLP content relations API
		 */
		do_action(
			self::TMWP_MLP_UPDATED_POST,
			$translated_post,
			$data,
			$this->site_relations,
			$this->content_relations
		);

	}

	/**
	 * @wp-hook tmwp_get_current_language
	 *
	 * @return Language
	 */
	public function current_language() {

		$site_id   = get_current_site()->id;
		$lang_iso  = mlp_get_blog_language( $site_id, false );
		$lang_name = mlp_get_lang_by_iso( $lang_iso );

		return new Language( $lang_iso, $lang_name );
	}

	/**
	 * @wp-hook tmwp_get_languages
	 *
	 * @param array $languages
	 * @param int   $site_id
	 *
	 * @return Language[]
	 */
	public function related_sites( $languages, $site_id ) {

		$sites = $this->site_relations->get_related_sites( $site_id );

		foreach ( $sites as $site ) {
			$lang_iso = mlp_get_blog_language( $site, false );

			$languages[ $site ] = new Language( $lang_iso, mlp_get_lang_by_iso( $lang_iso ) );
		}

		return $languages;
	}
}