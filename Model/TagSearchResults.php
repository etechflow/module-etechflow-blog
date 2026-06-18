<?php
/**
 * Typed search-results container for tags.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\TagSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class TagSearchResults extends SearchResults implements TagSearchResultsInterface
{
}
