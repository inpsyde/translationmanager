<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Unit\Module\WooCommerce\Processor;

use Brain\Monkey\Functions;
use Translationmanager\Module\WooCommerce\Integrator;
use Translationmanager\Module\WooCommerce\Processor\OutgoingMetaProcessor;
use Translationmanager\Translation;
use TranslationmanagerTests\TestCase;

/**
 * Class OutgoingMetaProcessorTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class OutgoingMetaProcessorTest extends TestCase
{
    /**
     * Test Prepare Products Outgoing Data
     */
    public function testProcessOutgoing()
    {
        {
            $productId = 1;
            $productPurchaseNote = 'Product Purchase Note';

            $translation = $this
                ->getMockBuilder(Translation::class)
                ->disableOriginalConstructor()
                ->setMethods(['source_post_id', 'set_value'])
                ->getMock();

            $product = $this
                ->getMockBuilder('WC_Product')
                ->setMethods(['get_purchase_note'])
                ->getMock();

            $testee = new OutgoingMetaProcessor();
        }

        {
            $translation
                ->expects($this->once())
                ->method('source_post_id')
                ->willReturn($productId);

            Functions\expect('wc_get_product')
                ->once()
                ->with($productId)
                ->andReturn($product);

            $product
                ->expects($this->once())
                ->method('get_purchase_note')
                ->willReturn($productPurchaseNote);

            $translation
                ->expects($this->once())
                ->method('set_value')
                ->with(Integrator::PRODUCT_META_PURCHASE_NOTE, $productPurchaseNote)
                ->willReturn(null);
        }

        {
            $testee->processOutgoing($translation);
        }
    }
}
