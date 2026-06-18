<?php
/**
 * Tag model.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\TagInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Tag extends AbstractModel implements TagInterface, IdentityInterface
{
    public const CACHE_TAG = 'etechflow_blog_tag';

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED  = 1;

    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'etechflow_blog_tag';
    protected $_eventObject = 'tag';

    protected function _construct()
    {
        $this->_init(\Etechflow\Blog\Model\ResourceModel\Tag::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return $this->getData(self::TAG_ID) === null ? null : (int)$this->getData(self::TAG_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::TAG_ID, $id);
    }

    public function getTitle(): ?string
    {
        $v = $this->getData(self::TITLE);
        return $v === null ? null : (string)$v;
    }

    public function setTitle(string $title): TagInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getIdentifier(): ?string
    {
        $v = $this->getData(self::IDENTIFIER);
        return $v === null ? null : (string)$v;
    }

    public function setIdentifier(string $identifier): TagInterface
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): TagInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive ? 1 : 0);
    }

    public function getMetaTitle(): ?string
    {
        $v = $this->getData(self::META_TITLE);
        return $v === null ? null : (string)$v;
    }

    public function setMetaTitle(?string $metaTitle): TagInterface
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    public function getMetaDescription(): ?string
    {
        $v = $this->getData(self::META_DESCRIPTION);
        return $v === null ? null : (string)$v;
    }

    public function setMetaDescription(?string $metaDescription): TagInterface
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }
}
