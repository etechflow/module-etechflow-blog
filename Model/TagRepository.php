<?php
/**
 * Tag repository implementation.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\TagRepositoryInterface;
use Etechflow\Blog\Api\Data\TagInterface;
use Etechflow\Blog\Api\Data\TagSearchResultsInterface;
use Etechflow\Blog\Api\Data\TagSearchResultsInterfaceFactory;
use Etechflow\Blog\Model\ResourceModel\Tag as TagResource;
use Etechflow\Blog\Model\ResourceModel\Tag\Collection;
use Etechflow\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class TagRepository implements TagRepositoryInterface
{
    /** @var TagResource */
    private $resource;
    /** @var TagFactory */
    private $tagFactory;
    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var TagSearchResultsInterfaceFactory */
    private $searchResultsFactory;
    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    public function __construct(
        TagResource $resource,
        TagFactory $tagFactory,
        CollectionFactory $collectionFactory,
        TagSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->tagFactory = $tagFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(TagInterface $tag): TagInterface
    {
        try {
            $this->resource->save($tag);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save the tag: %1', $e->getMessage()), $e);
        }
        return $tag;
    }

    public function getById(int $tagId): TagInterface
    {
        $tag = $this->tagFactory->create();
        $this->resource->load($tag, $tagId);
        if (!$tag->getId()) {
            throw new NoSuchEntityException(__('The tag with ID "%1" does not exist.', $tagId));
        }
        return $tag;
    }

    public function getByIdentifier(string $identifier, ?int $storeId = null): TagInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('identifier', $identifier);
        if ($storeId !== null) {
            $collection->addStoreFilter($storeId);
        }
        $tag = $collection->getFirstItem();
        if (!$tag->getId()) {
            throw new NoSuchEntityException(__('The tag "%1" does not exist.', $identifier));
        }
        return $tag;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): TagSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var TagSearchResultsInterface $results */
        $results = $this->searchResultsFactory->create();
        $results->setSearchCriteria($searchCriteria);
        $results->setItems($collection->getItems());
        $results->setTotalCount($collection->getSize());
        return $results;
    }

    public function delete(TagInterface $tag): bool
    {
        try {
            $this->resource->delete($tag);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the tag: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function deleteById(int $tagId): bool
    {
        return $this->delete($this->getById($tagId));
    }
}
