<?php
/**
 * Request-scoped holder for the "current" blog entity. Controllers set it,
 * blocks read it — avoids the deprecated global registry.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\AuthorInterface;
use Etechflow\Blog\Api\Data\CategoryInterface;
use Etechflow\Blog\Api\Data\PostInterface;
use Etechflow\Blog\Api\Data\TagInterface;

class PageContext
{
    public const TYPE_INDEX    = 'index';
    public const TYPE_POST     = 'post';
    public const TYPE_CATEGORY = 'category';
    public const TYPE_TAG      = 'tag';
    public const TYPE_AUTHOR   = 'author';
    public const TYPE_ARCHIVE  = 'archive';
    public const TYPE_SEARCH   = 'search';

    /** @var string */
    private $pageType = self::TYPE_INDEX;
    /** @var PostInterface|null */
    private $post;
    /** @var CategoryInterface|null */
    private $category;
    /** @var TagInterface|null */
    private $tag;
    /** @var AuthorInterface|null */
    private $author;
    /** @var string|null */
    private $archiveYear;
    /** @var string|null */
    private $archiveMonth;
    /** @var string */
    private $searchQuery = '';

    public function getPageType(): string
    {
        return $this->pageType;
    }

    public function setPageType(string $type): self
    {
        $this->pageType = $type;
        return $this;
    }

    public function getPost(): ?PostInterface
    {
        return $this->post;
    }

    public function setPost(PostInterface $post): self
    {
        $this->post = $post;
        $this->pageType = self::TYPE_POST;
        return $this;
    }

    public function getCategory(): ?CategoryInterface
    {
        return $this->category;
    }

    public function setCategory(CategoryInterface $category): self
    {
        $this->category = $category;
        $this->pageType = self::TYPE_CATEGORY;
        return $this;
    }

    public function getTag(): ?TagInterface
    {
        return $this->tag;
    }

    public function setTag(TagInterface $tag): self
    {
        $this->tag = $tag;
        $this->pageType = self::TYPE_TAG;
        return $this;
    }

    public function getAuthor(): ?AuthorInterface
    {
        return $this->author;
    }

    public function setAuthor(AuthorInterface $author): self
    {
        $this->author = $author;
        $this->pageType = self::TYPE_AUTHOR;
        return $this;
    }

    public function getArchiveYear(): ?string
    {
        return $this->archiveYear;
    }

    public function getArchiveMonth(): ?string
    {
        return $this->archiveMonth;
    }

    public function setArchive(string $year, ?string $month = null): self
    {
        $this->archiveYear = $year;
        $this->archiveMonth = $month;
        $this->pageType = self::TYPE_ARCHIVE;
        return $this;
    }

    public function getSearchQuery(): string
    {
        return $this->searchQuery;
    }

    public function setSearchQuery(string $query): self
    {
        $this->searchQuery = $query;
        $this->pageType = self::TYPE_SEARCH;
        return $this;
    }
}
