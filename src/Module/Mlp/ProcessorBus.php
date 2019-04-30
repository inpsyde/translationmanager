<?php // -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp;

use Translationmanager\TranslationData;

class ProcessorBus {

	/**
	 * @var \SplQueue<Processor\Processor>
	 */
	private $processors;

	/**
	 * @param Processor\Processor $processor
	 *
	 * @return ProcessorBus
	 */
	public function push_processor( Processor\Processor $processor ) {

		$this->processors or $this->processors = new \SplQueue();
		$this->processors->enqueue( $processor );

		return $this;
	}

	public function process( TranslationData $data, Adapter $adapter ) {

		$is_incoming = $data->is_incoming();

		if ( ! $is_incoming && ! $data->is_outgoing() ) {
			return;
		}

		/**
		 * Fires before processors runs.
		 *
		 * Use this hook to add processors by calling `push_processor()` on passed bus instance.
		 *
		 * @param ProcessorBus    $processor_bus
		 * @param TranslationData $data
		 */
		do_action( 'translationmanager_mlp_data_processors', $this, $data );

		if ( ! $this->processors ) {
			return;
		}

		while ( $this->processors->count() ) {

			/** @var Processor\IncomingProcessor|Processor\OutgoingProcessor $processor */
			$processor = $this->processors->dequeue();

			$target = $is_incoming ? '\\Processor\\IncomingProcessor' : '\\Processor\\OutgoingProcessor';
			$method = $is_incoming ? 'process_incoming' : 'process_outgoing';

			if (
				is_a( $processor, __NAMESPACE__ . $target )
				&& apply_filters( 'translationmanager_mlp_data_processor_enabled', true, $processor, $data )
			) {
				do_action(
					'translationmanager_log',
					[
						'message' => 'Processing with ' . get_class( $processor ) . '::' . $method . '()',
						'context' => [
							'Target site' => $data->target_site_id(),
							'Source site' => $data->source_site_id(),
							'Source post' => $data->source_post_id(),
							'Target lang' => $data->target_language(),
						],
					]
				);

				/** @var callable $cb */
				$cb = [ $processor, $method ];
				$cb( $data, $adapter );
			}
		}

	}

}
