<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Functional\Functional\Module\WooCommerce;

use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Translationmanager\Module\WooCommerce\Integrator;
use Translationmanager\Translation;
use TranslationmanagerTests\TestCase;
use Translationmanager\Module\WooCommerce\Processor\IncomingMetaProcessor as Testee;

/**
 * Class IncomingMetaProcessorTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class IncomingMetaProcessorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Test Incoming Meta Data Calls the Meta Update Methods and Perform Save Operation for the Product
     */
    public function testIncomingMetaDataAreSaved()
    {
        {
            $targetSiteId = 2;

            $product = $this
                ->getMockBuilder('WC_Product')
                ->setMethods(['set_purchase_note', 'save'])
                ->getMock();

            $translation = $this
                ->getMockBuilder(Translation::class)
                ->disableOriginalConstructor()
                ->setMethods(['is_valid', 'target_site_id'])
                ->getMock();

            $testee = $this->createTesteeInstance(['updatePurchaseNoteMeta', 'product']);
        }

        {
            $translation
                ->expects($this->once())
                ->method('is_valid')
                ->willReturn(true);

            $testee
                ->expects($this->once())
                ->method('product')
                ->willReturn($product);

            $translation
                ->expects($this->once())
                ->method('target_site_id')
                ->willReturn($targetSiteId);

            $this->expectNetworkState(1, 2);

            $testee
                ->expects($this->once())
                ->method('updatePurchaseNoteMeta')
                ->with($translation, $product);

            $product
                ->expects($this->once())
                ->method('save')
                ->willReturn(null);
        }

        {
            $testee->processIncoming($translation);
        }
    }

    /**
     * Test Update Purchase Note Meta is Performed Without Problems
     */
    public function testUpdatePurchaseNoteMeta()
    {
        {
            $expectedPurchaseNoteMessage = 'Purchase note';

            $translation = $this
                ->getMockBuilder(Translation::class)
                ->disableOriginalConstructor()
                ->setMethods(['get_value'])
                ->getMock();

            $product = $this
                ->getMockBuilder('WC_Product')
                ->disableOriginalConstructor()
                ->setMethods(['set_purchase_note'])
                ->getMock();

            list($testee, $testeeMethod) = $this->createTesteeToTestProtectedMethods(
                Testee::class,
                [],
                ['updatePurchaseNoteMeta']
            );
        }

        {
            $translation
                ->expects($this->once())
                ->method('get_value')
                ->with(
                    Integrator::PRODUCT_META_PURCHASE_NOTE,
                    Integrator::DATA_NAMESPACE
                )
                ->willReturn($expectedPurchaseNoteMessage);

            $product
                ->expects($this->once())
                ->method('set_purchase_note')
                ->with($expectedPurchaseNoteMessage)
                ->willReturn(true);
        }

        {
            $testeeMethod->invoke($testee, $translation, $product);
        }
    }

    /**
     * Set Expectations for Network State
     *
     * @param $sourceSiteId
     * @param $targetSiteId
     */
    private function expectNetworkState($sourceSiteId, $targetSiteId)
    {
        Functions\expect('get_current_blog_id')
            ->twice()
            ->andReturn($sourceSiteId)
            ->andAlsoExpectIt()
            ->once()
            ->andReturn($targetSiteId);

        Functions\expect('switch_to_blog')
            ->once()
            ->with($targetSiteId)
            ->andAlsoExpectIt()
            ->once()
            ->with($sourceSiteId);
    }

    /**
     * Create Testee instance
     *
     * @param $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function createTesteeInstance($methods)
    {
        return $this
            ->getMockBuilder(Testee::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }
}
