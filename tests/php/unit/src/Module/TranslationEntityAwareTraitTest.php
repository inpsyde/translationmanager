<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Unit\Module;

use Brain\Monkey\Functions;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Translationmanager\Exception\UnexpectedEntityException;
use Translationmanager\Module\Mlp\Processor\PostSaver;
use Translationmanager\Module\ModuleIntegrator;
use Translationmanager\Module\TranslationEntityAwareTrait as Testee;
use Translationmanager\Translation;
use TranslationmanagerTests\TestCase;

/**
 * Class TranslationEntityAwareTraitTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class TranslationEntityAwareTraitTest extends TestCase
{
    /**
     * Test Retrieval of Post Entity
     */
    public function testRetrievalOfPostEntity()
    {
        {
            $post = $this->getMockBuilder('WP_Post')->getMock();
            $post->ID = 1;

            $translation = $this
                ->getMockBuilder(Translation::class)
                ->disableOriginalConstructor()
                ->setMethods(['get_meta'])
                ->getMock();

            $testee = $this->getMockForTrait(Testee::class);
            $methodTestee = $this->traitMethodReflectionFor($testee, 'post');
        }

        {
            $translation
                ->expects($this->once())
                ->method('get_meta')
                ->with(
                    PostSaver::SAVED_POST_KEY,
                    ModuleIntegrator::POST_DATA_NAMESPACE
                )
                ->willReturn($post);
        }

        {
            $response = $methodTestee->invokeArgs($testee, [$translation]);
        }

        {
            self::assertSame($post, $response);
        }
    }

    /**
     * Test Retrieval of Post Entity Rise an Exception if Post Cannot be Retrieved
     */
    public function testRetrievalPostThrownException()
    {
        {
            $translation = $this
                ->getMockBuilder(Translation::class)
                ->disableOriginalConstructor()
                ->setMethods(['get_meta'])
                ->getMock();

            $testee = $this->getMockForTrait(Testee::class);
            $methodTestee = $this->traitMethodReflectionFor($testee, 'post');
        }

        {
            $translation
                ->expects($this->once())
                ->method('get_meta')
                ->with(
                    PostSaver::SAVED_POST_KEY,
                    ModuleIntegrator::POST_DATA_NAMESPACE
                )
                ->willReturn(null);

            $this->expectException(UnexpectedEntityException::class);
            $this->expectExceptionMessage('Unexpected post value retrieved for post type WP_Post');
        }

        {
            $methodTestee->invokeArgs($testee, [$translation]);
        }
    }

    /**
     * Test Retrieval of Product Entity
     */
    public function testRetrievalForProductEntity()
    {
        {
            $post = $this->getMockBuilder('WP_Post')->getMock();
            $post->ID = 1;

            $product = $this->getMockBuilder('WC_Product')->getMock();

            $translation = $this->createMock(Translation::class);

            $testee = $this->getMockForTrait(
                Testee::class,
                [],
                '',
                false,
                false,
                true,
                [
                    'post',
                ]
            );
            $methodTestee = $this->traitMethodReflectionFor($testee, 'product');
        }

        {
            $testee
                ->expects($this->once())
                ->method('post')
                ->with($translation)
                ->willReturn($post);

            Functions\expect('wc_get_product')
                ->once()
                ->with($post)
                ->andReturn($product);
        }

        {
            $response = $methodTestee->invokeArgs($testee, [$translation]);
        }

        {
            self::assertSame($product, $response);
        }
    }

    /**
     * Test Retrieval of Product Entity Rise an Exception if Product Cannot be Retrieved
     */
    public function testRetrievalProductThrowException()
    {
        {
            $post = $this
                ->getMockBuilder('WP_Post')
                ->getMock();
            $post->ID = 1;

            $translation = $this->createMock(Translation::class);

            $testee = $this->getMockForTrait(
                Testee::class,
                [],
                '',
                false,
                false,
                true,
                [
                    'post',
                ]
            );
            $methodTestee = $this->traitMethodReflectionFor($testee, 'product');
        }

        {
            $testee
                ->expects($this->once())
                ->method('post')
                ->with($translation)
                ->willReturn($post);

            Functions\expect('wc_get_product')
                ->once()
                ->with($post)
                ->andReturn(null);

            $this->expectException(UnexpectedEntityException::class);
            $this->expectExceptionMessage('Unexpected post value retrieved for post type WC_Product');
        }

        {
            $methodTestee->invokeArgs($testee, [$translation]);
        }
    }

    /**
     * Build Instance for Trait Test
     *
     * @param PHPUnit_Framework_MockObject_MockObject $testeeInstance
     * @param string $testeeMethod
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    private function traitMethodReflectionFor($testeeInstance, $testeeMethod)
    {
        $testeeClass = get_class($testeeInstance);

        $reflectionClass = new ReflectionClass($testeeClass);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PROTECTED);

        foreach ($methods as $method) {
            $method->getName() !== $testeeMethod and $method->setAccessible(true);
        }

        $methodReflection = new ReflectionMethod($testeeClass, $testeeMethod);
        $methodReflection->setAccessible(true);

        return $methodReflection;
    }
}
