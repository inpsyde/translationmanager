<?php # -*- coding: utf-8 -*-

namespace Tmwp\Module\Mlp\Processor;

use Tmwp\Module\Mlp\Connector;
use Tmwp\Translation_Data;

class Taxonomies_Sync implements Incoming_Processor {

	/**
	 * @param Translation_Data       $data
	 * @param \Mlp_Site_Relations    $site_relations
	 * @param \Mlp_Content_Relations $content_relations
	 *
	 * @return void
	 */
	public function process_incoming(
		Translation_Data $data,
		\Mlp_Site_Relations $site_relations,
		\Mlp_Content_Relations $content_relations
	) {

		$target_post = $this->check_incoming_data( $data );

		if ( ! $target_post ) {
			return;
		}

		switch_to_blog( $data->source_site_id() );

		list ( $target_post_term_tt_ids, $target_post_new_terms ) = $this->query_linked_terms(
			$data,
			$this->query_source_term_taxonomy_ids( $data ),
			$content_relations
		);

		restore_current_blog();

		switch_to_blog( $data->target_site_id() );

		$this->sync_target_terms(
			$target_post_term_tt_ids,
			$this->relate_new_terms( $target_post_new_terms, $data, $content_relations ),
			$target_post
		);

		restore_current_blog();
	}

	/**
	 * Do sanity checks and extract the target post from data object.
	 *
	 * @param Translation_Data $data
	 *
	 * @return \WP_Post|null Target post on successful check.
	 */
	private function check_incoming_data( Translation_Data $data ) {

		/** @var \WP_Post $target_post */
		$target_post = $data->get_meta( Post_Saver::SAVED_POST_KEY, Connector::DATA_NAMESPACE );

		if ( ! $target_post instanceof \WP_Post || ! $target_post->ID ) {
			return null;
		}

		$sync_on_update = true;
		if ( $data->get_meta( Post_Data_Builder::IS_UPDATE_KEY, Connector::DATA_NAMESPACE ) ) {
			$sync_on_update = apply_filters( 'tmwp_mlp_module_sync_taxonomies_on_update', true, $data );
		}

		if ( ! $sync_on_update ) {
			return null;
		}

		$post_types_to_sync = apply_filters(
			'tmwp_mlp_module_sync_taxonomies_post_types',
			array_merge(
				array( 'post', 'page' ),
				get_post_types( array( 'public' => true, '_builtin' => false ) )
			),
			$data
		);

		if ( ! in_array( $target_post->post_type, $post_types_to_sync, true ) ) {
			return null;
		}

		return $target_post;
	}

	/**
	 * Runs in the context of source site to extract the term taxonomy ids of terms from all public taxonomies
	 * of source post.
	 *
	 * Taxonomies to target can be filtered.
	 * Run in the context of source post site.
	 *
	 * @param Translation_Data $data
	 *
	 * @return int[]
	 */
	private function query_source_term_taxonomy_ids( Translation_Data $data ) {

		$source_post = $data->source_post();

		if ( ! $source_post ) {
			return array();
		}

		/** @var \WP_Taxonomy[] $taxonomies */
		$taxonomies = get_object_taxonomies( $source_post, 'objects' );

		if ( ! $taxonomies ) {
			return array();
		}

		$taxonomies_to_sync = array();
		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy->public and $taxonomies_to_sync[] = $taxonomy->name;
		}

		$taxonomies_to_sync = apply_filters(
			'tmwp_mlp_module_sync_taxonomies',
			$taxonomies_to_sync,
			$data
		);

		if ( ! $taxonomies_to_sync ) {
			return array();
		}

		$term_query = new \WP_Term_Query();

		$terms = $term_query->query(
			array(
				'taxonomy'   => $taxonomies_to_sync,
				'object_ids' => array( $source_post->ID ),
				'hide_empty' => false,
				'fields'     => 'tt_ids'
			)
		);

		return $terms;
	}

	/**
	 * Use content relation to get term objects to associate to target post.
	 *
	 * If no existing relation is found, allow filters to create a target term "on the flight".
	 * Run in the context of source post site.
	 *
	 * @param Translation_Data       $data
	 * @param array                  $source_term_taxonomy_ids Source post terms term taxonomy ids,
	 *                                                         from query_source_term_taxonomy_ids
	 * @param \Mlp_Content_Relations $content_relations
	 *
	 * @return array    Two items array.
	 *                  First item is an array of term taxonomy id for existing terms in target site
	 *                  which are related to terms assigned to source post and so should be assigned to target post.
	 *                  Second element is an array of term objects which are generated "on the fly" and should be
	 *                  assigned to target post. Maybe its' necessary to store in the DB first.
	 */
	private function query_linked_terms(
		Translation_Data $data,
		$source_term_taxonomy_ids,
		\Mlp_Content_Relations $content_relations
	) {

		$source_site_id = $data->source_site_id();
		$target_site_id = $data->target_site_id();
		$target_post_term_tt_ids  = $target_post_new_terms = array();

		// Loop through source term ids to find a related term on target site
		foreach ( $source_term_taxonomy_ids as $source_tt_id ) {

			$linked_term_tt_ids = $content_relations->get_relations( $source_site_id, $source_tt_id, 'term' );

			// If a linked term is found, store its id and continue looping
			if ( ! empty( $linked_term_tt_ids[ $target_site_id ] ) ) {
				$linked_tt_id = $linked_term_tt_ids[ $target_site_id ];
				is_numeric( $linked_tt_id ) and $target_post_term_tt_ids[] = (int) $linked_tt_id;

				continue;
			}

			// Linked term is not found, let's see if source term is valid
			$source_term = get_term_by( 'term_taxonomy_id', $source_tt_id );
			if ( ! $source_term instanceof \WP_Term ) {
				continue;
			}

			// If source term is valid, let's see if 3rd parties can provide "on the fly" a term to use
			$new_term = apply_filters( 'tmwp_mlp_module_sync_taxonomies_create_terms', null, $source_term, $data );
			if ( $new_term instanceof \WP_Term && taxonomy_exists( $new_term->taxonomy ) ) {
				$target_post_new_terms[ $source_term->term_taxonomy_id ] = $new_term;
			}
		}

		return array( $target_post_term_tt_ids, $target_post_new_terms );
	}

	/**
	 * Receive an array where keys are term taxonomy ids of post in source site and values are term objects, in the context
	 * of target site, that need to be related to terms in keys.
	 * Term object might not be saved yet, in that case need to be saved before setting relation.
	 *
	 * Run in the context of target post site.
	 *
	 * @param \WP_Term[]             $terms_to_relate
	 * @param Translation_Data       $data
	 * @param \Mlp_Content_Relations $content_relations
	 *
	 * @return int[] Term ids of terms that need to be associated to target post
	 */
	private function relate_new_terms(
		array $terms_to_relate,
		Translation_Data $data,
		\Mlp_Content_Relations $content_relations
	) {

		$source_site_id = $data->source_site_id();
		$target_site_id = $data->target_site_id();
		$target_terms   = array();

		foreach ( $terms_to_relate as $source_term_tt_id => $term_to_relate ) {

			if ( empty( $target_terms[ $term_to_relate->taxonomy ] ) ) {
				$target_terms[ $term_to_relate->taxonomy ] = array();
			}

			// We got an existing term, just set relation and store its id in target terms to be returned
			if ( $term_to_relate->term_id && $term_to_relate->term_taxonomy_id ) {

				$content_relations->set_relation(
					$source_site_id,
					$target_site_id,
					$source_term_tt_id,
					$term_to_relate->term_taxonomy_id,
					'term'
				);

				$target_terms[ $term_to_relate->taxonomy ][] = (int) $term_to_relate->term_id;
				continue;
			}

			// Let's check if slug we got exists, if so, set relation and store its id in target terms to be returned
			if ( $term_to_relate->slug ) {
				$term_exist = get_term_by( 'slug', $term_to_relate->slug, $term_to_relate->taxonomy );
				if ( $term_exist instanceof \WP_Term ) {
					$content_relations->set_relation(
						$source_site_id,
						$target_site_id,
						$source_term_tt_id,
						$term_to_relate->term_taxonomy_id,
						'term'
					);
					$target_terms[ $term_to_relate->taxonomy ][] = (int) $term_exist->term_id;
					continue;
				}
			}

			// If here, the term object to relate is not saved yet. Let's save it...
			$insert = wp_insert_term(
				$term_to_relate->name,
				$term_to_relate->taxonomy,
				array(
					'slug'        => $term_to_relate->slug,
					'parent'      => $term_to_relate->parent,
					'description' => $term_to_relate->description,
				)
			);
			// ... and if saved correctly, set relation, then store its id in target terms to be returned
			if ( is_array( $insert ) && ! empty( $insert[ 'term_id' ] ) && ! empty( $insert[ 'term_taxonomy_id' ] ) ) {
				$content_relations->set_relation(
					$source_site_id,
					$target_site_id,
					$source_term_tt_id,
					$term_to_relate->term_taxonomy_id,
					'term'
				);
				$target_terms[ $term_to_relate->taxonomy ][] = (int) $insert[ 'term_id' ];
			}
		}

		return array_filter( $target_terms );
	}

	/**
	 * Receive two array with terms in the target site that are related to term in source site associated to source post,
	 * and so need to be synced to target post.
	 *
	 * The first array contains term taxonomy ids, so we need to get the term id first.
	 * The second array contains already term ids, grouped by taxonomy, whose names are used as array keys.
	 * Run in the context of target post site.
	 *
	 * @param int[]            $linked_tt_ids
	 * @param int[][]          $target_term_ids
	 * @param \WP_Post         $target_post
	 */
	private function sync_target_terms(
		array $linked_tt_ids,
		array $target_term_ids,
		\WP_Post $target_post
	) {

		// When no linked terms, nor target terms, nothing else is left to do
		if ( ! $linked_tt_ids && ! $target_term_ids ) {
			return;
		}

		// If linked terms where found, let's switch to target site and obtain term objects to store and later return

		$linked_tt_ids and $linked_tt_ids = array_unique( $linked_tt_ids );

		foreach ( $linked_tt_ids as $linked_tt_id ) {

			$linked_term = get_term_by( 'term_taxonomy_id', $linked_tt_id );
			if ( ! $linked_term instanceof \WP_Term ) {
				continue;
			}

			if ( ! array_key_exists( $linked_term->taxonomy, $target_term_ids ) ) {
				$target_term_ids[ $linked_term->taxonomy ] = array();
			}

			$target_term_ids[ $linked_term->taxonomy ][] = (int) $linked_term->term_id;
		}

		foreach ( $target_term_ids as $taxonomy => $term_ids ) {
			$term_ids = wp_parse_id_list( $term_ids );
			$term_ids and wp_set_object_terms( $target_post->ID, $term_ids, $taxonomy, false );
		}
	}
}