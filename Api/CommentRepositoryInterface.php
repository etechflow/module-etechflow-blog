<?php
/**
 * Comment repository service contract — CRUD + list.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api;

use Etechflow\Blog\Api\Data\CommentInterface;
use Etechflow\Blog\Api\Data\CommentSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface CommentRepositoryInterface
{
    public function save(CommentInterface $comment): CommentInterface;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $commentId): CommentInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): CommentSearchResultsInterface;

    public function delete(CommentInterface $comment): bool;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $commentId): bool;
}
