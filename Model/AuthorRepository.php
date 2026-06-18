<?php
/**
 * Author repository implementation.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\AuthorRepositoryInterface;
use Etechflow\Blog\Api\Data\AuthorInterface;
use Etechflow\Blog\Api\Data\AuthorSearchResultsInterface;
use Etechflow\Blog\Api\Data\AuthorSearchResultsInterfaceFactory;
use Etechflow\Blog\Model\ResourceModel\Author as AuthorResource;
use Etechflow\Blog\Model\ResourceModel\Author\Collection;
use Etechflow\Blog\Model\ResourceModel\Author\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class AuthorRepository implements AuthorRepositoryInterface
{
    /** @var AuthorResource */
    private $resource;
    /** @var AuthorFactory */
    private $authorFactory;
    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var AuthorSearchResultsInterfaceFactory */
    private $searchResultsFactory;
    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    public function __construct(
        AuthorResource $resource,
        AuthorFactory $authorFactory,
        CollectionFactory $collectionFactory,
        AuthorSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->authorFactory = $authorFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(AuthorInterface $author): AuthorInterface
    {
        try {
            $this->resource->save($author);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save the author: %1', $e->getMessage()), $e);
        }
        return $author;
    }

    public function getById(int $authorId): AuthorInterface
    {
        $author = $this->authorFactory->create();
        $this->resource->load($author, $authorId);
        if (!$author->getId()) {
            throw new NoSuchEntityException(__('The author with ID "%1" does not exist.', $authorId));
        }
        return $author;
    }

    public function getByIdentifier(string $identifier, ?int $storeId = null): AuthorInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('identifier', $identifier);
        if ($storeId !== null) {
            $collection->addStoreFilter($storeId);
        }
        $author = $collection->getFirstItem();
        if (!$author->getId()) {
            throw new NoSuchEntityException(__('The author "%1" does not exist.', $identifier));
        }
        return $author;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): AuthorSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var AuthorSearchResultsInterface $results */
        $results = $this->searchResultsFactory->create();
        $results->setSearchCriteria($searchCriteria);
        $results->setItems($collection->getItems());
        $results->setTotalCount($collection->getSize());
        return $results;
    }

    public function delete(AuthorInterface $author): bool
    {
        try {
            $this->resource->delete($author);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the author: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function deleteById(int $authorId): bool
    {
        return $this->delete($this->getById($authorId));
    }
}
