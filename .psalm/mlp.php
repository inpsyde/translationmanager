<?php

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Framework\Database\Exception {
    /**
     * Exception to be thrown when an action is to be performed on an table that doesn't exists.
     */
    class NonexistentTable extends \Exception
    {
        /**
         * NonexistentTable constructor.
         * @param string $action
         * @param string $table
         */
        public function __construct(string $action, string $table)
        {
        }
    }
}

namespace Inpsyde\MultilingualPress\Framework\Api {

    use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;

    /**
     * Interface for all content relations API implementations.
     */
    interface ContentRelations
    {
        /**
         * Creates a relationship for the given content ids provided as an array with site IDs as keys
         * and content IDs as values.
         *
         * @param int[] $contentIds
         * @param string $type
         * @return int
         * @throws NonexistentTable
         */
        public function createRelationship(array $contentIds, string $type): int;

        /**
         * Deletes all relations for content elements that don't exist (anymore).
         *
         * @param string $type
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteAllRelationsForInvalidContent(string $type): bool;

        /**
         * Deletes all relations for sites that don't exist (anymore).
         *
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteAllRelationsForInvalidSites(): bool;

        /**
         * Deletes all relations for the site with the given ID.
         *
         * @param int $siteId
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteAllRelationsForSite(int $siteId): bool;

        /**
         * Deletes a relation according to the given arguments.
         *
         * @param int[] $contentIds
         * @param string $type
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteRelation(array $contentIds, string $type): bool;

        /**
         * Copies all relations of the given (or any) content type from the given source site to the
         * given destination site.
         *
         * This method is suited to be used after site duplication, because both sites are assumed to
         * have the exact same content IDs.
         *
         * @param int $sourceSiteId
         * @param int $targetSiteId
         * @return int
         * @throws NonexistentTable
         */
        public function duplicateRelations(int $sourceSiteId, int $targetSiteId): int;

        /**
         * Returns the content ID for the given arguments.
         *
         * @param int $relationshipId
         * @param int $siteId
         * @return int
         * @throws NonexistentTable
         */
        public function contentId(int $relationshipId, int $siteId): int;

        /**
         * Returns the content ID in the given target site for the given content element.
         *
         * @param int $siteId
         * @param int $contentId
         * @param string $type
         * @param int $targetSiteId
         * @return int
         * @throws NonexistentTable
         */
        public function contentIdForSite(
            int    $siteId,
            int    $contentId,
            string $type,
            int    $targetSiteId
        ): int;

        /**
         * Returns the content IDs for the given relationship ID.
         *
         * @param int $relationshipId
         * @return int[]
         * @throws NonexistentTable
         */
        public function contentIds(int $relationshipId): array;

        /**
         * Returns all relations for the given content element.
         *
         * @param int $siteId
         * @param int $contentId
         * @param string $type
         * @return int[]
         * @throws NonexistentTable
         */
        public function relations(int $siteId, int $contentId, string $type): array;

        /**
         * Returns the relationship ID for the given arguments.
         *
         * @param int[] $contentIds
         * @param string $type
         * @param bool $create
         * @return int
         * @throws NonexistentTable
         */
        public function relationshipId(array $contentIds, string $type, bool $create = false): int;

        /**
         * Checks if the site with the given ID has any relations of the given (or any) content type.
         *
         * @param int $siteId
         * @param string $type
         * @return bool
         * @throws NonexistentTable
         */
        public function hasSiteRelations(int $siteId, string $type = ''): bool;

        /**
         * Relates all posts between the given source site and the given destination site.
         *
         * This method is suited to be used after site duplication, because both sites are assumed to
         * have the exact same post IDs.
         * Furthermore, the current site is assumed to be either the source site or the destination site.
         *
         * @param int $sourceSite
         * @param int $targetSite
         * @return bool
         * @throws NonexistentTable
         */
        public function relateAllPosts(int $sourceSite, int $targetSite): bool;

        /**
         * Relates all terms between the given source site and the given destination site.
         *
         * This method is suited to be used after site duplication, because both sites are assumed to
         * have the exact same term taxonomy IDs.
         * Furthermore, the current site is assumed to be either the source site or the destination site.
         *
         * @param int $sourceSite
         * @param int $targetSite
         * @return bool
         * @throws NonexistentTable
         */
        public function relateAllTerms(int $sourceSite, int $targetSite): bool;

        /**
         * Sets a relation according to the given arguments.
         *
         * @param int $relationshipId
         * @param int $siteId
         * @param int $contentId
         * @return bool
         * @throws NonexistentTable
         */
        public function saveRelation(int $relationshipId, int $siteId, int $contentId): bool;
    }
}

namespace Inpsyde\MultilingualPress\Framework\Language {
    /**
     * Interface for all language data type implementations.
     */
    interface Language
    {
        const ISO_SHORTEST = 'iso_shortest';

        /**
         * Returns the ID of the language.
         *
         * @return int
         */
        public function id(): int;

        /**
         * Checks if the language is written right-to-left (RTL).
         *
         * @return bool
         */
        public function isRtl(): bool;

        /**
         * Returns the language name.
         *
         * @return string
         */
        public function name(): string;

        /**
         * Returns the language name.
         *
         * @return string
         */
        public function englishName(): string;

        /**
         * Returns the language name.
         *
         * @return string
         */
        public function nativeName(): string;

        /**
         * Returns the language ISO 639 code.
         *
         * @param string $which
         * @return string
         */
        public function isoCode(string $which = self::ISO_SHORTEST): string;

        /**
         * Returns the language name to be used for frontend purposes.
         *
         * @return string
         */
        public function isoName(): string;

        /**
         * Returns the language BCP-47 tag.
         *
         * @return string
         */
        public function bcp47tag(): string;

        /**
         * Returns the language locale.
         *
         * @return string
         */
        public function locale(): string;

        /**
         * Returns the language type.
         *
         * @return string
         */
        public function type(): string;
    }
}

namespace Inpsyde\MultilingualPress\Framework\Service {

    /**
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    class ServiceProvidersCollection implements \Countable
    {
        public function __construct()
        {
        }

        /**
         * Adds the given service provider to the collection.
         *
         * @param ServiceProvider $provider
         * @return ServiceProvidersCollection
         */
        public function add(ServiceProvider $provider): ServiceProvidersCollection
        {
        }

        /**
         * Removes the given service provider from the collection.
         *
         * @param ServiceProvider $provider
         * @return ServiceProvidersCollection
         */
        public function remove(ServiceProvider $provider): ServiceProvidersCollection
        {
        }

        /**
         * Calls the method with the given name on all registered providers,
         * and passes on potential further arguments.
         *
         * @param string $methodName
         * @param array ...$args
         */
        public function applyMethod(string $methodName, ...$args)
        {
        }

        /**
         * Executes the given callback for all registered providers,
         * and passes along potential further arguments.
         *
         * @param callable $callback
         * @param array ...$args
         */
        public function applyCallback(callable $callback, ...$args)
        {
        }

        /**
         * Executes the given callback for all registered providers, and returns the instance that
         * contains the providers that passed the filtering.
         *
         * @param callable $callback
         * @param array ...$args
         * @return ServiceProvidersCollection
         */
        public function filter(callable $callback, ...$args): ServiceProvidersCollection
        {
        }

        /**
         * Executes the given callback for all registered providers, and returns the instance that
         * contains the providers obtained.
         *
         * @param callable $callback
         * @param array ...$args
         * @return ServiceProvidersCollection
         * @throws \UnexpectedValueException If a given callback did not return a service provider instance.
         */
        public function map(callable $callback, ...$args): ServiceProvidersCollection
        {
        }

        /**
         * Executes the given callback for all registered providers, and passes along the result of
         * previous callback.
         *
         * @param callable $callback
         * @param mixed $initial
         * @return mixed
         */
        public function reduce(callable $callback, $initial = null)
        {
        }

        /**
         * Returns the number of providers in the collection.
         *
         * @return int
         */
        public function count(): int
        {
        }
    }

    /**
     * Interface for all service provider implementations to be used for dependency management.
     */
    interface ServiceProvider
    {
        /**
         * Registers the provided services on the given container.
         *
         * @param Container $container
         */
        public function register(Container $container);
    }

    /**
     * Interface for all bootstrappable service provider implementations.
     */
    interface BootstrappableServiceProvider extends ServiceProvider
    {
        /**
         * Bootstraps the registered services.
         *
         * @param Container $container
         */
        public function bootstrap(Container $container);
    }
}