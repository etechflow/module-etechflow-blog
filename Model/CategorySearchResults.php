<?php
/**
 * Typed search-results container for categories.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\CategorySearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class CategorySearchResults extends SearchResults implements CategorySearchResultsInterface
{
}
