<?php
/**
 * Containing the class translation data value object.
 *
 * @package tmwp
 */

namespace Tmwp;

final class Translation_Data implements \ArrayAccess, \JsonSerializable {

	const META_KEY = '__meta';
	const VALUES_KEY = '__values';
	const SOURCE_POST_ID_KEY = 'source_post_id';
	const SOURCE_POST_KEY = 'source_post_obj';
	const SOURCE_SITE_KEY = 'source_site_id';
	const TARGET_POST_KEY = 'target_post_id';
	const TARGET_SITE_KEY = 'target_site_id';
	const TARGET_LANG_KEY = 'target_language';
	const INCOMING = 'incoming';
	const OUTGOING = 'outgoing';

	private static $protected_meta = array(
		self::SOURCE_POST_ID_KEY,
		self::SOURCE_POST_KEY,
		self::SOURCE_SITE_KEY,
		self::TARGET_POST_KEY,
		self::TARGET_SITE_KEY,
		self::TARGET_LANG_KEY,
	);

	/**
	 * @var array
	 */
	private $storage = array(
		self::META_KEY   => array(
			self::SOURCE_POST_ID_KEY => 0,
			self::SOURCE_SITE_KEY    => 0,
			self::TARGET_SITE_KEY    => 0,
			self::TARGET_LANG_KEY    => '',
		),
		self::VALUES_KEY => array(),
	);

	/**
	 * @var string
	 */
	private $direction = '';

	/**
	 * Disabled on purpose, use named constructor.
	 */
	private function __construct() {

	}

	/**
	 * @param \WP_Post $source_post
	 * @param int      $source_site_id
	 * @param int      $target_site_id
	 * @param string   $target_language
	 * @param array    $outgoing_data
	 *
	 * @return static
	 * @internal param array $meta
	 */
	public static function for_outgoing(
		\WP_Post $source_post,
		$source_site_id,
		$target_site_id,
		$target_language,
		array $outgoing_data = array()
	) {

		$embedded_meta = array_key_exists( self::META_KEY, $outgoing_data )
			? (array) $outgoing_data[ self::META_KEY ]
			: array();

		$meta = array(
			self::SOURCE_POST_ID_KEY => (int) $source_post->ID,
			self::SOURCE_SITE_KEY    => (int) $source_site_id,
			self::TARGET_SITE_KEY    => (int) $target_site_id,
			self::TARGET_LANG_KEY    => (string) $target_language,
		);

		unset( $outgoing_data[ self::META_KEY ] );

		$outgoing_data = array_merge( $outgoing_data, array(
			'post_title'   => $source_post->post_title,
			'post_content' => $source_post->post_content,
			'post_excerpt' => $source_post->post_excerpt,
		) );

		$instance            = new static();
		$instance->direction = self::OUTGOING;
		$instance->storage   = array(
			self::META_KEY   => array_merge( $embedded_meta, $meta ),
			self::VALUES_KEY => $outgoing_data,
		);

		return $instance;
	}

	/**
	 * @param array $incoming_data
	 *
	 * @return static
	 */
	public static function for_incoming( array $incoming_data ) {

		$embedded_meta = array_key_exists( self::META_KEY, $incoming_data )
			? (array) $incoming_data[ self::META_KEY ]
			: array();

		$empty_meta = array(
			self::SOURCE_POST_ID_KEY => 0,
			self::SOURCE_SITE_KEY    => 0,
			self::TARGET_SITE_KEY    => 0,
			self::TARGET_LANG_KEY    => '',
		);

		$meta = array_merge( $empty_meta, $embedded_meta );

		$meta[ self::SOURCE_POST_ID_KEY ] = (int) $meta[ self::SOURCE_POST_ID_KEY ];
		$meta[ self::SOURCE_SITE_KEY ]    = (int) $meta[ self::SOURCE_SITE_KEY ];
		$meta[ self::TARGET_SITE_KEY ]    = (int) $meta[ self::TARGET_SITE_KEY ];
		$meta[ self::TARGET_LANG_KEY ]    = (string) $meta[ self::TARGET_LANG_KEY ];

		unset( $incoming_data[ self::META_KEY ] );

		$instance            = new static();
		$instance->direction = self::INCOMING;
		$instance->storage   = array( self::META_KEY => $meta, self::VALUES_KEY => $incoming_data );

		return $instance;

	}

	/**
	 * @return bool
	 */
	public function is_incoming() {

		return $this->direction === self::INCOMING;
	}

	/**
	 * @return bool
	 */
	public function is_outgoing() {

		return $this->direction === self::OUTGOING;
	}

	/**
	 * @return bool
	 */
	public function is_valid() {

		return
			$this->source_post_id()
			&& $this->source_site_id()
			&& $this->target_site_id()
			&& $this->target_language();
	}

	/**
	 * @return int
	 */
	public function source_post_id() {

		return $this->storage[ self::META_KEY ][ self::SOURCE_POST_ID_KEY ];
	}

	/**
	 * @return int
	 */
	public function source_site_id() {

		return $this->storage[ self::META_KEY ][ self::SOURCE_SITE_KEY ];
	}

	/**
	 * @return \WP_Post|null
	 */
	public function source_post() {

		if ( ! empty( $this->storage[ self::META_KEY ][ self::SOURCE_POST_KEY ] ) ) {
			return $this->storage[ self::META_KEY ][ self::SOURCE_POST_KEY ];
		}

		if ( ! $this->is_valid() ) {
			return null;
		}

		$site_id  = $this->source_site_id();
		$switched = $site_id !== get_current_blog_id();
		$switched and switch_to_blog( $site_id );
		$post = get_post( $this->source_post_id() );
		$switched and restore_current_blog();
		$this->storage[ self::META_KEY ][ self::SOURCE_POST_KEY ] = $post ? $post : null;

		// If source post does not return a valid post, we invalidate the data by unsetting post id
		if ( ! $post ) {
			$this->storage[ self::META_KEY ][ self::SOURCE_POST_ID_KEY ] = null;
		}

		return $this->storage[ self::META_KEY ][ self::SOURCE_POST_KEY ];
	}

	/**
	 * @return int
	 */
	public function target_site_id() {

		return $this->storage[ self::META_KEY ][ self::TARGET_SITE_KEY ];
	}

	/**
	 * @return int
	 */
	public function target_language() {

		return $this->storage[ self::META_KEY ][ self::TARGET_LANG_KEY ];
	}

	/**
	 * @param string $key
	 * @param string $namespace
	 *
	 * @return bool
	 */
	public function has_value( $key, $namespace = '' ) {

		$storage = $this->storage[ self::VALUES_KEY ];
		if ( $namespace && ! isset( $storage[ $namespace ] ) ) {
			return false;
		}

		return $namespace ? array_key_exists( $key, $storage[ $namespace ] ) : array_key_exists( $key, $storage );

	}

	/**
	 * @param string $key
	 * @param string $namespace
	 *
	 * @return mixed
	 */
	public function get_value( $key, $namespace = '' ) {

		if ( ! $this->has_value( $key, $namespace ) ) {
			return null;
		}

		$storage = $this->storage[ self::VALUES_KEY ];
		if ( $namespace && ! isset( $storage[ $namespace ] ) ) {
			return null;
		} elseif ( $namespace ) {
			$storage = $storage[ $namespace ];
		}

		return $storage[ $key ];
	}

	/**
	 * @param        $key
	 * @param        $value
	 * @param string $namespace
	 */
	public function set_value( $key, $value, $namespace = '' ) {

		$storage = &$this->storage[ self::VALUES_KEY ];

		if ( $namespace ) {
			isset( $storage[ $namespace ] ) or $storage[ $namespace ] = array();
			$storage = &$storage[ $namespace ];
		}

		$storage[ $key ] = $value;
	}

	/**
	 * @param string $key
	 * @param string $namespace
	 */
	public function remove_value( $key, $namespace = '' ) {

		$storage = &$this->storage[ self::VALUES_KEY ];

		if ( $namespace ) {
			isset( $storage[ $namespace ] ) or $storage[ $namespace ] = array();
			$storage = &$storage[ $namespace ];
		}

		unset( $storage[ $key ] );
	}

	/**
	 * @param string $key
	 * @param string $namespace
	 *
	 * @return bool
	 */
	public function has_meta( $key, $namespace = '' ) {

		$storage = $this->storage[ self::META_KEY ];
		if ( $namespace && ! isset( $storage[ $namespace ] ) ) {
			return false;
		}

		return $namespace ? array_key_exists( $key, $storage[ $namespace ] ) : array_key_exists( $key, $storage );

	}

	/**
	 * @param string $key
	 * @param string $namespace
	 *
	 * @return mixed
	 */
	public function get_meta( $key, $namespace = '' ) {

		if ( ! $this->has_meta( $key, $namespace ) ) {
			return null;
		}

		$storage = $this->storage[ self::META_KEY ];
		if ( $namespace && ! isset( $storage[ $namespace ] ) ) {
			return null;
		} elseif ( $namespace ) {
			$storage = $storage[ $namespace ];
		}

		return $storage[ $key ];
	}

	/**
	 * @param        $key
	 * @param        $value
	 * @param string $namespace
	 */
	public function set_meta( $key, $value, $namespace = '' ) {

		if ( ! $namespace && in_array( $key, self::$protected_meta, true ) ) {
			_doing_it_wrong( __METHOD__, "Meta key {$key} is protected and can't be overridden.", '0.1' );

			return;
		}

		$storage = &$this->storage[ self::META_KEY ];

		if ( $namespace ) {
			isset( $storage[ $namespace ] ) or $storage[ $namespace ] = array();
			$storage = &$storage[ $namespace ];
		}

		$storage[ $key ] = $value;
	}

	/**
	 * @param string $key
	 * @param string $namespace
	 */
	public function remove_meta( $key, $namespace = '' ) {

		if ( ! $namespace && in_array( $key, self::$protected_meta, true ) ) {
			_doing_it_wrong( __METHOD__, "Meta key {$key} is protected and can't be removed.", '0.1' );

			return;
		}

		$storage = &$this->storage[ self::META_KEY ];

		if ( $namespace ) {
			isset( $storage[ $namespace ] ) or $storage[ $namespace ] = array();
			$storage = &$storage[ $namespace ];
		}

		unset( $storage[ $key ] );
	}

	/**
	 * @return array
	 */
	public function to_array() {

		$data = $this->storage[ self::VALUES_KEY ];

		$data[ self::META_KEY ] = $this->storage[ self::META_KEY ];

		return $data;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetExists( $offset ) {

		return $this->has_value( $offset );
	}

	/**
	 * @inheritdoc
	 */
	public function offsetGet( $offset ) {

		return $this->get_value( $offset );
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet( $offset, $value ) {

		$this->set_value( $offset, $value );
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset( $offset ) {

		$this->remove_value( $offset );
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {

		return $this->to_array();
	}
}