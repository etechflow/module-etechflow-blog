<?php
/**
 * Author repository service contract — CRUD + list.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api;

use Etechflow\Blog\Api\Data\AuthorInterface;
use Etechflow\Blog\Api\Data\AuthorSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface AuthorRepositoryInterface
{
    public function save(AuthorInterface $author): AuthorInterface;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $authorId): AuthorInterface;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByIdentifier(string $identifier, ?int $storeId = null): AuthorInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): AuthorSearchResultsInterface;

    public function delete(AuthorInterface $author): bool;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $authorId): bool;
}
