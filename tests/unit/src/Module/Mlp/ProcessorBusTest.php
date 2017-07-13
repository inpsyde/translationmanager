<?php # -*- coding: utf-8 -*-

namespace Tmwp\Tests\Module\Mlp;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Tmwp\Module\Mlp\Processor\Incoming_Processor;
use Tmwp\Module\Mlp\Processor\Outgoing_Processor;
use Tmwp\Tests\TestCase;
use Tmwp\Translation_Data;
use Tmwp\Module\Mlp\Processor_Bus;

class ProcessorBusTest extends TestCase {

	public function test_process_fires_hooks() {

		$bus = new Processor_Bus();

		$data = Translation_Data::for_incoming( [] );

		/** @var \Mlp_Site_Relations $site_relations */
		$site_relations = \Mockery::mock( \Mlp_Site_Relations::class );
		/** @var \Mlp_Content_Relations $content_relations */
		$content_relations = \Mockery::mock( \Mlp_Content_Relations::class );

		Filters\expectApplied( 'tmwp_mlp_data_processor_enabled' )
			->once()
			->with( true, \Mockery::type( Incoming_Processor::class ), $data )
			->andReturnFirstArg();

		Actions\expectDone( 'tmwp_mlp_data_processors' )
			->once()
			->with( \Mockery::type( Processor_Bus::class ), $data )
			->whenHappen( function ( Processor_Bus $bus ) use ( $data, $site_relations, $content_relations ) {

				/** @var Incoming_Processor|\Mockery\MockInterface $processor */
				$processor = \Mockery::mock( Incoming_Processor::class );
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

		/** @var Outgoing_Processor|\Mockery\MockInterface $processor */
		$processor = \Mockery::mock( Outgoing_Processor::class );
		$processor->shouldReceive( 'process_outgoing' )->never();

		$bus = new Processor_Bus();
		$bus->push_processor( $processor );

		$data = Translation_Data::for_incoming( [] );

		/** @var \Mlp_Site_Relations $site_relations */
		$site_relations = \Mockery::mock( \Mlp_Site_Relations::class );
		/** @var \Mlp_Content_Relations $content_relations */
		$content_relations = \Mockery::mock( \Mlp_Content_Relations::class );

		Filters\expectApplied( 'tmwp_mlp_data_processor_enabled' )->never();

		Actions\expectDone( 'tmwp_mlp_data_processors' )
			->once()
			->with( \Mockery::type( Processor_Bus::class ), $data );

		$bus->process( $data, $site_relations, $content_relations );
	}

	public function test_processor_can_be_skipped_via_filter() {

		/** @var Outgoing_Processor|\Mockery\MockInterface $processor_a */
		$processor_a = \Mockery::mock( Incoming_Processor::class );
		$processor_a->shouldReceive( 'process_incoming' )->never();

		/** @var Outgoing_Processor|\Mockery\MockInterface $processor_b */
		$processor_b = \Mockery::mock( Incoming_Processor::class );
		$processor_b->shouldReceive( 'process_incoming' )->once()->andReturnUsing(function() {
			echo 'Processor B executed!';
		});

		$bus = new Processor_Bus();
		$bus->push_processor( $processor_a )->push_processor( $processor_b );

		$data = Translation_Data::for_incoming( [] );

		/** @var \Mlp_Site_Relations $site_relations */
		$site_relations = \Mockery::mock( \Mlp_Site_Relations::class );
		/** @var \Mlp_Content_Relations $content_relations */
		$content_relations = \Mockery::mock( \Mlp_Content_Relations::class );

		Filters\expectApplied( 'tmwp_mlp_data_processor_enabled' )
			->twice()
			->andReturnUsing( function ($true, $processor) use($processor_a) {
				return $processor_a === $processor ? false : $true;
			});

		$this->expectOutputString( 'Processor B executed!' );

		$bus->process( $data, $site_relations, $content_relations );
	}
}