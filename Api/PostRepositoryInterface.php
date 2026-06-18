<?php
/**
 * Post repository service contract — CRUD + list, the supported way for other
 * code to read/write posts (keeps callers decoupled from the resource model).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api;

use Etechflow\Blog\Api\Data\PostInterface;
use Etechflow\Blog\Api\Data\PostSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface PostRepositoryInterface
{
    public function save(PostInterface $post): PostInterface;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $postId): PostInterface;

    /**
     * Load a published post by its URL identifier for a given store.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByIdentifier(string $identifier, ?int $storeId = null): PostInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): PostSearchResultsInterface;

    public function delete(PostInterface $post): bool;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $postId): bool;
}
