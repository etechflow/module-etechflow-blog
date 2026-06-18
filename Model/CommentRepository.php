<?php
/**
 * Comment repository implementation.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\CommentRepositoryInterface;
use Etechflow\Blog\Api\Data\CommentInterface;
use Etechflow\Blog\Api\Data\CommentSearchResultsInterface;
use Etechflow\Blog\Api\Data\CommentSearchResultsInterfaceFactory;
use Etechflow\Blog\Model\ResourceModel\Comment as CommentResource;
use Etechflow\Blog\Model\ResourceModel\Comment\Collection;
use Etechflow\Blog\Model\ResourceModel\Comment\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CommentRepository implements CommentRepositoryInterface
{
    /** @var CommentResource */
    private $resource;
    /** @var CommentFactory */
    private $commentFactory;
    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var CommentSearchResultsInterfaceFactory */
    private $searchResultsFactory;
    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    public function __construct(
        CommentResource $resource,
        CommentFactory $commentFactory,
        CollectionFactory $collectionFactory,
        CommentSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->commentFactory = $commentFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(CommentInterface $comment): CommentInterface
    {
        try {
            $this->resource->save($comment);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save the comment: %1', $e->getMessage()), $e);
        }
        return $comment;
    }

    public function getById(int $commentId): CommentInterface
    {
        $comment = $this->commentFactory->create();
        $this->resource->load($comment, $commentId);
        if (!$comment->getId()) {
            throw new NoSuchEntityException(__('The comment with ID "%1" does not exist.', $commentId));
        }
        return $comment;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): CommentSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CommentSearchResultsInterface $results */
        $results = $this->searchResultsFactory->create();
        $results->setSearchCriteria($searchCriteria);
        $results->setItems($collection->getItems());
        $results->setTotalCount($collection->getSize());
        return $results;
    }

    public function delete(CommentInterface $comment): bool
    {
        try {
            $this->resource->delete($comment);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the comment: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function deleteById(int $commentId): bool
    {
        return $this->delete($this->getById($commentId));
    }
}
