<?php
/**
 * Typed search-results container for comments.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\CommentSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class CommentSearchResults extends SearchResults implements CommentSearchResultsInterface
{
}
