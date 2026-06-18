<?php
/**
 * Typed search-results container for comments.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface CommentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Etechflow\Blog\Api\Data\CommentInterface[]
     */
    public function getItems();

    /**
     * @param \Etechflow\Blog\Api\Data\CommentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
