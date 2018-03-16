<?php # -*- coding: utf-8 -*-
// phpcs:disable
namespace Translationmanager\Tests\Unit\Module\Mlp;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Translationmanager\Module\Mlp\Processor\IncomingProcessor;
use Translationmanager\Module\Mlp\Processor\OutgoingProcessor;
use Translationmanager\Tests\TestCase;
use Translationmanager\TranslationData;
use Translationmanager\Module\Mlp\ProcessorBus;

class ProcessorBusTest extends TestCase {

	public function test_process_fires_hooks() {

		$bus = new ProcessorBus();

		$data = TranslationData::for_incoming( [] );

		/** @var \Mlp_Site_Relations $site_relations */
		$site_relations = \Mockery::mock( \Mlp_Site_Relations::class );
		/** @var \Mlp_Content_Relations $content_relations */
		$content_relations = \Mockery::mock( \Mlp_Content_Relations::class );

		Filters\expectApplied( 'translationmanager_mlp_data_processor_enabled' )
			->once()
			->with( true, \Mockery::type( IncomingProcessor::class ), $data )
			->andReturnFirstArg();

		Actions\expectDone( 'translationmanager_mlp_data_processors' )
			->once()
			->with( \Mockery::type( ProcessorBus::class ), $data )
			->whenHappen( function ( ProcessorBus $bus ) use ( $data, $site_relations, $content_relations ) {

				/** @var IncomingProcessor|\Mockery\MockInterface $processor */
				$processor = \Mockery::mock( IncomingProcessor::class );
				$processor
					->shouldReceive( 'process_incoming' )
					->once()
					->with( $data, $site_relations, $content_relations )
					->andReturnUsing( function () {
						echo 'Process happened!';
					} );

				$bus->push_processor( $processor );
			} );

		$this->expectOutputString( 'Process happened!' );

		$bus->process( $data, $site_relations, $content_relations );
	}

	public function test_process_incoming_data_not_executes_outgoing_processors() {

		/** @var OutgoingProcessor|\Mockery\MockInterface $processor */
		$processor = \Mockery::mock( OutgoingProcessor::class );
		$processor->shouldReceive( 'process_outgoing' )->never();

		$bus = new ProcessorBus();
		$bus->push_processor( $processor );

		$data = TranslationData::for_incoming( [] );

		/** @var \Mlp_Site_Relations $site_relations */
		$site_relations = \Mockery::mock( \Mlp_Site_Relations::class );
		/** @var \Mlp_Content_Relations $content_relations */
		$content_relations = \Mockery::mock( \Mlp_Content_Relations::class );

		Filters\expectApplied( 'translationmanager_mlp_data_processor_enabled' )->never();

		Actions\expectDone( 'translationmanager_mlp_data_processors' )
			->once()
			->with( \Mockery::type( ProcessorBus::class ), $data );

		$bus->process( $data, $site_relations, $content_relations );
	}

	public function test_processor_can_be_skipped_via_filter() {

		/** @var OutgoingProcessor|\Mockery\MockInterface $processor_a */
		$processor_a = \Mockery::mock( IncomingProcessor::class );
		$processor_a->shouldReceive( 'process_incoming' )->never();

		/** @var OutgoingProcessor|\Mockery\MockInterface $processor_b */
		$processor_b = \Mockery::mock( IncomingProcessor::class );
		$processor_b->shouldReceive( 'process_incoming' )->once()->andReturnUsing(function() {
			echo 'Processor B executed!';
		});

		$bus = new ProcessorBus();
		$bus->push_processor( $processor_a )->push_processor( $processor_b );

		$data = TranslationData::for_incoming( [] );

		/** @var \Mlp_Site_Relations $site_relations */
		$site_relations = \Mockery::mock( \Mlp_Site_Relations::class );
		/** @var \Mlp_Content_Relations $content_relations */
		$content_relations = \Mockery::mock( \Mlp_Content_Relations::class );

		Filters\expectApplied( 'translationmanager_mlp_data_processor_enabled' )
			->twice()
			->andReturnUsing( function ($true, $processor) use($processor_a) {
				return $processor_a === $processor ? false : $true;
			});

		$this->expectOutputString( 'Processor B executed!' );

		$bus->process( $data, $site_relations, $content_relations );
	}
}