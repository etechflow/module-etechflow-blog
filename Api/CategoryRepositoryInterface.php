<?php
/**
 * Category repository service contract — CRUD + list.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api;

use Etechflow\Blog\Api\Data\CategoryInterface;
use Etechflow\Blog\Api\Data\CategorySearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface CategoryRepositoryInterface
{
    public function save(CategoryInterface $category): CategoryInterface;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $categoryId): CategoryInterface;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByIdentifier(string $identifier, ?int $storeId = null): CategoryInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): CategorySearchResultsInterface;

    public function delete(CategoryInterface $category): bool;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $categoryId): bool;
}
