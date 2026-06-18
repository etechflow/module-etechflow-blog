<?php
/**
 * Category repository implementation.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\CategoryRepositoryInterface;
use Etechflow\Blog\Api\Data\CategoryInterface;
use Etechflow\Blog\Api\Data\CategorySearchResultsInterface;
use Etechflow\Blog\Api\Data\CategorySearchResultsInterfaceFactory;
use Etechflow\Blog\Model\ResourceModel\Category as CategoryResource;
use Etechflow\Blog\Model\ResourceModel\Category\Collection;
use Etechflow\Blog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CategoryRepository implements CategoryRepositoryInterface
{
    /** @var CategoryResource */
    private $resource;
    /** @var CategoryFactory */
    private $categoryFactory;
    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var CategorySearchResultsInterfaceFactory */
    private $searchResultsFactory;
    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    public function __construct(
        CategoryResource $resource,
        CategoryFactory $categoryFactory,
        CollectionFactory $collectionFactory,
        CategorySearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->categoryFactory = $categoryFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(CategoryInterface $category): CategoryInterface
    {
        try {
            $this->resource->save($category);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save the category: %1', $e->getMessage()), $e);
        }
        return $category;
    }

    public function getById(int $categoryId): CategoryInterface
    {
        $category = $this->categoryFactory->create();
        $this->resource->load($category, $categoryId);
        if (!$category->getId()) {
            throw new NoSuchEntityException(__('The category with ID "%1" does not exist.', $categoryId));
        }
        return $category;
    }

    public function getByIdentifier(string $identifier, ?int $storeId = null): CategoryInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('identifier', $identifier);
        if ($storeId !== null) {
            $collection->addStoreFilter($storeId);
        }
        $category = $collection->getFirstItem();
        if (!$category->getId()) {
            throw new NoSuchEntityException(__('The category "%1" does not exist.', $identifier));
        }
        return $category;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): CategorySearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CategorySearchResultsInterface $results */
        $results = $this->searchResultsFactory->create();
        $results->setSearchCriteria($searchCriteria);
        $results->setItems($collection->getItems());
        $results->setTotalCount($collection->getSize());
        return $results;
    }

    public function delete(CategoryInterface $category): bool
    {
        try {
            $this->resource->delete($category);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the category: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function deleteById(int $categoryId): bool
    {
        return $this->delete($this->getById($categoryId));
    }
}
