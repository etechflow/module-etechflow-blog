<?php
/**
 * Category model.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\CategoryInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Category extends AbstractModel implements CategoryInterface, IdentityInterface
{
    public const CACHE_TAG = 'etechflow_blog_category';

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED  = 1;

    public const DM_POSTS          = 'POSTS';
    public const DM_PAGE           = 'PAGE';
    public const DM_POSTS_AND_PAGE = 'POSTS_AND_PAGE';

    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'etechflow_blog_category';
    protected $_eventObject = 'category';

    protected function _construct()
    {
        $this->_init(\Etechflow\Blog\Model\ResourceModel\Category::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return $this->getData(self::CATEGORY_ID) === null ? null : (int)$this->getData(self::CATEGORY_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::CATEGORY_ID, $id);
    }

    public function getTitle(): ?string
    {
        $v = $this->getData(self::TITLE);
        return $v === null ? null : (string)$v;
    }

    public function setTitle(string $title): CategoryInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getIdentifier(): ?string
    {
        $v = $this->getData(self::IDENTIFIER);
        return $v === null ? null : (string)$v;
    }

    public function setIdentifier(string $identifier): CategoryInterface
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    public function getParentId(): int
    {
        return (int)$this->getData(self::PARENT_ID);
    }

    public function setParentId(int $parentId): CategoryInterface
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): CategoryInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive ? 1 : 0);
    }

    public function getIncludeInMenu(): bool
    {
        return (bool)$this->getData(self::INCLUDE_IN_MENU);
    }

    public function setIncludeInMenu(bool $include): CategoryInterface
    {
        return $this->setData(self::INCLUDE_IN_MENU, $include ? 1 : 0);
    }

    public function getContent(): ?string
    {
        $v = $this->getData(self::CONTENT);
        return $v === null ? null : (string)$v;
    }

    public function setContent(?string $content): CategoryInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    public function getMetaTitle(): ?string
    {
        $v = $this->getData(self::META_TITLE);
        return $v === null ? null : (string)$v;
    }

    public function setMetaTitle(?string $metaTitle): CategoryInterface
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    public function getMetaDescription(): ?string
    {
        $v = $this->getData(self::META_DESCRIPTION);
        return $v === null ? null : (string)$v;
    }

    public function setMetaDescription(?string $metaDescription): CategoryInterface
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }
}
