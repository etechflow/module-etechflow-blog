<?php
/**
 * Typed search-results container for tags.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface TagSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Etechflow\Blog\Api\Data\TagInterface[]
     */
    public function getItems();

    /**
     * @param \Etechflow\Blog\Api\Data\TagInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
