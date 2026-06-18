<?php
/**
 * Typed search-results container for authors.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface AuthorSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Etechflow\Blog\Api\Data\AuthorInterface[]
     */
    public function getItems();

    /**
     * @param \Etechflow\Blog\Api\Data\AuthorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
