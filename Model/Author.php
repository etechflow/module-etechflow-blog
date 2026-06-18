<?php
/**
 * Author model.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\AuthorInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Author extends AbstractModel implements AuthorInterface, IdentityInterface
{
    public const CACHE_TAG = 'etechflow_blog_author';

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED  = 1;

    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'etechflow_blog_author';
    protected $_eventObject = 'author';

    protected function _construct()
    {
        $this->_init(\Etechflow\Blog\Model\ResourceModel\Author::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return $this->getData(self::AUTHOR_ID) === null ? null : (int)$this->getData(self::AUTHOR_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::AUTHOR_ID, $id);
    }

    public function getTitle(): ?string
    {
        $v = $this->getData(self::TITLE);
        return $v === null ? null : (string)$v;
    }

    public function setTitle(string $title): AuthorInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getIdentifier(): ?string
    {
        $v = $this->getData(self::IDENTIFIER);
        return $v === null ? null : (string)$v;
    }

    public function setIdentifier(string $identifier): AuthorInterface
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): AuthorInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive ? 1 : 0);
    }

    public function getImage(): ?string
    {
        $v = $this->getData(self::IMAGE);
        return $v === null ? null : (string)$v;
    }

    public function setImage(?string $image): AuthorInterface
    {
        return $this->setData(self::IMAGE, $image);
    }

    public function getContent(): ?string
    {
        $v = $this->getData(self::CONTENT);
        return $v === null ? null : (string)$v;
    }

    public function setContent(?string $content): AuthorInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    public function getEmail(): ?string
    {
        $v = $this->getData(self::EMAIL);
        return $v === null ? null : (string)$v;
    }

    public function setEmail(?string $email): AuthorInterface
    {
        return $this->setData(self::EMAIL, $email);
    }
}
