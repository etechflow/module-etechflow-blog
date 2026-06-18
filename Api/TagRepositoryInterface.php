<?php
/**
 * Tag repository service contract — CRUD + list.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api;

use Etechflow\Blog\Api\Data\TagInterface;
use Etechflow\Blog\Api\Data\TagSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface TagRepositoryInterface
{
    public function save(TagInterface $tag): TagInterface;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $tagId): TagInterface;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByIdentifier(string $identifier, ?int $storeId = null): TagInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): TagSearchResultsInterface;

    public function delete(TagInterface $tag): bool;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $tagId): bool;
}
