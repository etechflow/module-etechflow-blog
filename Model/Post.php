<?php
/**
 * Post model. Implements the service contract and Magento's IdentityInterface
 * so full-page-cache correctly invalidates the post when it changes.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\PostInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Post extends AbstractModel implements PostInterface, IdentityInterface
{
    public const CACHE_TAG = 'etechflow_blog_post';

    /** Status values */
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED  = 1;

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = 'etechflow_blog_post';

    protected $_eventObject = 'post';

    protected function _construct()
    {
        $this->_init(\Etechflow\Blog\Model\ResourceModel\Post::class);
    }

    /**
     * Cache identities — drives FPC/block_html invalidation on save.
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return $this->getData(self::POST_ID) === null ? null : (int)$this->getData(self::POST_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::POST_ID, $id);
    }

    public function getTitle(): ?string
    {
        $v = $this->getData(self::TITLE);
        return $v === null ? null : (string)$v;
    }

    public function setTitle(string $title): PostInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getIdentifier(): ?string
    {
        $v = $this->getData(self::IDENTIFIER);
        return $v === null ? null : (string)$v;
    }

    public function setIdentifier(string $identifier): PostInterface
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): PostInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive ? 1 : 0);
    }

    public function getContentHeading(): ?string
    {
        $v = $this->getData(self::CONTENT_HEADING);
        return $v === null ? null : (string)$v;
    }

    public function setContentHeading(?string $heading): PostInterface
    {
        return $this->setData(self::CONTENT_HEADING, $heading);
    }

    public function getContent(): ?string
    {
        $v = $this->getData(self::CONTENT);
        return $v === null ? null : (string)$v;
    }

    public function setContent(?string $content): PostInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    public function getShortContent(): ?string
    {
        $v = $this->getData(self::SHORT_CONTENT);
        return $v === null ? null : (string)$v;
    }

    public function setShortContent(?string $shortContent): PostInterface
    {
        return $this->setData(self::SHORT_CONTENT, $shortContent);
    }

    public function getMetaTitle(): ?string
    {
        $v = $this->getData(self::META_TITLE);
        return $v === null ? null : (string)$v;
    }

    public function setMetaTitle(?string $metaTitle): PostInterface
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    public function getMetaKeywords(): ?string
    {
        $v = $this->getData(self::META_KEYWORDS);
        return $v === null ? null : (string)$v;
    }

    public function setMetaKeywords(?string $metaKeywords): PostInterface
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    public function getMetaDescription(): ?string
    {
        $v = $this->getData(self::META_DESCRIPTION);
        return $v === null ? null : (string)$v;
    }

    public function setMetaDescription(?string $metaDescription): PostInterface
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    public function getFeaturedImg(): ?string
    {
        $v = $this->getData(self::FEATURED_IMG);
        return $v === null ? null : (string)$v;
    }

    public function setFeaturedImg(?string $img): PostInterface
    {
        return $this->setData(self::FEATURED_IMG, $img);
    }

    public function getAuthorId()
    {
        $v = $this->getData(self::AUTHOR_ID);
        return $v === null ? null : (int)$v;
    }

    public function setAuthorId($authorId): PostInterface
    {
        return $this->setData(self::AUTHOR_ID, $authorId);
    }

    public function getViewsCount(): int
    {
        return (int)$this->getData(self::VIEWS_COUNT);
    }

    public function setViewsCount(int $views): PostInterface
    {
        return $this->setData(self::VIEWS_COUNT, $views);
    }

    public function getPublishTime(): ?string
    {
        $v = $this->getData(self::PUBLISH_TIME);
        return $v === null ? null : (string)$v;
    }

    public function setPublishTime(?string $publishTime): PostInterface
    {
        return $this->setData(self::PUBLISH_TIME, $publishTime);
    }

    public function getCreationTime(): ?string
    {
        $v = $this->getData(self::CREATION_TIME);
        return $v === null ? null : (string)$v;
    }

    public function getUpdateTime(): ?string
    {
        $v = $this->getData(self::UPDATE_TIME);
        return $v === null ? null : (string)$v;
    }
}
