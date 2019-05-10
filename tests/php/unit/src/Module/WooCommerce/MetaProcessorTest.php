<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Unit\Module\WooCommerce;

use Brain\Monkey\Functions;
use Translationmanager\Module\WooCommerce\Bridge;
use Translationmanager\Module\WooCommerce\Processor\OutgoingMetaProcessor;
use Translationmanager\Translation;
use TranslationmanagerTests\TestCase;

/**
 * Class BridgeTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class MetaProcessorTest extends TestCase
{
    /**
     * Test Bridge Prepare Products Outgoing Data
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
                ->with('purchase_note', $productPurchaseNote)
                ->willReturn(null);
        }

        {
            $testee->processOutgoing($translation);
        }
    }
}
