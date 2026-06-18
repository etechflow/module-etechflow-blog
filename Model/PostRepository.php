<?php
/**
 * Post repository implementation. Single supported entry point for reading and
 * writing posts; keeps callers decoupled from the resource model. PHP 7.4
 * compatible (no constructor promotion) for broad version support.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\PostInterface;
use Etechflow\Blog\Api\Data\PostSearchResultsInterface;
use Etechflow\Blog\Api\Data\PostSearchResultsInterfaceFactory;
use Etechflow\Blog\Api\PostRepositoryInterface;
use Etechflow\Blog\Model\ResourceModel\Post as PostResource;
use Etechflow\Blog\Model\ResourceModel\Post\Collection;
use Etechflow\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PostRepository implements PostRepositoryInterface
{
    /** @var PostResource */
    private $resource;
    /** @var PostFactory */
    private $postFactory;
    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var PostSearchResultsInterfaceFactory */
    private $searchResultsFactory;
    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    public function __construct(
        PostResource $resource,
        PostFactory $postFactory,
        CollectionFactory $collectionFactory,
        PostSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->postFactory = $postFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(PostInterface $post): PostInterface
    {
        try {
            $this->resource->save($post);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save the post: %1', $e->getMessage()), $e);
        }
        return $post;
    }

    public function getById(int $postId): PostInterface
    {
        $post = $this->postFactory->create();
        $this->resource->load($post, $postId);
        if (!$post->getId()) {
            throw new NoSuchEntityException(__('The post with ID "%1" does not exist.', $postId));
        }
        return $post;
    }

    public function getByIdentifier(string $identifier, ?int $storeId = null): PostInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('identifier', $identifier);
        if ($storeId !== null) {
            $collection->addStoreFilter($storeId);
        }
        $post = $collection->getFirstItem();
        if (!$post->getId()) {
            throw new NoSuchEntityException(__('The post "%1" does not exist.', $identifier));
        }
        return $post;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): PostSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var PostSearchResultsInterface $results */
        $results = $this->searchResultsFactory->create();
        $results->setSearchCriteria($searchCriteria);
        $results->setItems($collection->getItems());
        $results->setTotalCount($collection->getSize());
        return $results;
    }

    public function delete(PostInterface $post): bool
    {
        try {
            $this->resource->delete($post);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the post: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function deleteById(int $postId): bool
    {
        return $this->delete($this->getById($postId));
    }
}
