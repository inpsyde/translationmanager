<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Module;

use BadMethodCallException;
use Translationmanager\Exception\UnexpectedEntityException;
use Translationmanager\Module\Mlp\Processor\PostSaver;
use Translationmanager\Translation;
use WC_Product;
use WP_Post;

/**
 * Class TranslationEntityCapableTrait
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
trait TranslationEntityAwareTrait
{
    /**
     * Retrieve the Post Entity from Translation Data
     *
     * @param Translation $translation
     *
     * @return WP_Post
     * @throws UnexpectedEntityException
     */
    protected function post(Translation $translation)
    {
        $post = $translation->get_meta(
            PostSaver::SAVED_POST_KEY,
            ModuleIntegrator::POST_DATA_NAMESPACE
        );

        if (!$post instanceof WP_Post) {
            throw UnexpectedEntityException::forPostValue(WP_Post::class, '');
        }

        return $post;
    }

    /**
     * Retrieve the Product Entity from Translated Post
     *
     * @param Translation $translation
     *
     * @return WC_Product
     * @throws BadMethodCallException
     * @throws UnexpectedEntityException
     */
    protected function product(Translation $translation)
    {
        if (!function_exists('wc_get_product')) {
            throw new BadMethodCallException('Function wc_get_product does not exists.');
        }

        $post = $this->post($translation);
        $product = wc_get_product($post);

        if (!$product instanceof WC_Product) {
            throw UnexpectedEntityException::forPostValue(WC_Product::class, '');
        }

        return $product;
    }
}
