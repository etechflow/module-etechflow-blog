<?php
/**
 * Typed search-results container for Post lists (used by getList()).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface PostSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Etechflow\Blog\Api\Data\PostInterface[]
     */
    public function getItems();

    /**
     * @param \Etechflow\Blog\Api\Data\PostInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
