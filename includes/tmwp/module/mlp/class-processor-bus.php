<?php # -*- coding: utf-8 -*-

namespace Tmwp\Module\Mlp;

use Tmwp\Translation_Data;

class Processor_Bus {

	const INGOING = 'ingoing';
	const OUTGOING = 'outgoing';

	/**
	 * @var \SplQueue<Processor\Processor>
	 */
	private $processors;

	/**
	 * @param Processor\Processor $processor
	 *
	 * @return Processor_Bus
	 */
	public function push_processor( Processor\Processor $processor ) {

		$this->processors or $this->processors = new \SplQueue();
		$this->processors->enqueue( $processor );

		return $this;
	}

	public function process(
		$type,
		Translation_Data $data,
		\Mlp_Site_Relations $site_relations,
		\Mlp_Content_Relations $content_relations
	) {

		if ( ! in_array( $type, array( self::INGOING, self::OUTGOING ), true ) ) {
			return;
		}

		/**
		 * Fires before processors runs.
		 *
		 * Use this hook to add processors by calling `push_processor()` on passed bus instance.
		 *
		 * @param Processor_Bus $processor_bus
		 */
		do_action( 'tmwp_mlp_data_processors', $this, $type );

		if ( ! $this->processors ) {
			return;
		}

		while ( $this->processors->count() ) {

			/** @var Processor\Incoming_Processor|Processor\Outgoing_Processor $processor */
			$processor = $this->processors->dequeue();

			$target = $type === self::INGOING ? '\\Processor\\Incoming_Processor' : '\\Processor\\Outgoing_Processor';
			$method = $type === self::INGOING ? 'process_incoming' : 'process_outgoing';

			if ( is_a( $processor, __NAMESPACE__ . $target ) && $processor->enabled( $data ) ) {

				/** @var callable $cb */
				$cb = array( $processor, $method );
				$cb( $data, $site_relations, $content_relations );
			}
		}

	}

}