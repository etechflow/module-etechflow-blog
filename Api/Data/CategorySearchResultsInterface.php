<?php
/**
 * Typed search-results container for categories.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface CategorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Etechflow\Blog\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * @param \Etechflow\Blog\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
