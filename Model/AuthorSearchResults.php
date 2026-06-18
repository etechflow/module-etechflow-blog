<?php
/**
 * Typed search-results container for authors.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\AuthorSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class AuthorSearchResults extends SearchResults implements AuthorSearchResultsInterface
{
}
