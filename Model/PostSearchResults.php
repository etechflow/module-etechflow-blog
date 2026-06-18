<?php
/**
 * Typed search-results container for posts.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\PostSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class PostSearchResults extends SearchResults implements PostSearchResultsInterface
{
}
