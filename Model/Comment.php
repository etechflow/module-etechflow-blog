<?php
/**
 * Comment model with moderation status + author-type constants.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Etechflow\Blog\Api\Data\CommentInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Comment extends AbstractModel implements CommentInterface, IdentityInterface
{
    public const CACHE_TAG = 'etechflow_blog_comment';

    /** Moderation status */
    public const STATUS_PENDING      = 0;
    public const STATUS_APPROVED     = 1;
    public const STATUS_NOT_APPROVED = 2;

    /** Author type */
    public const TYPE_GUEST    = 0;
    public const TYPE_CUSTOMER = 1;
    public const TYPE_ADMIN    = 2;

    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'etechflow_blog_comment';
    protected $_eventObject = 'comment';

    protected function _construct()
    {
        $this->_init(\Etechflow\Blog\Model\ResourceModel\Comment::class);
    }

    public function getIdentities()
    {
        return [
            self::CACHE_TAG . '_' . $this->getId(),
            Post::CACHE_TAG . '_' . $this->getPostId(),
        ];
    }

    public function getId()
    {
        return $this->getData(self::COMMENT_ID) === null ? null : (int)$this->getData(self::COMMENT_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::COMMENT_ID, $id);
    }

    public function getPostId(): int
    {
        return (int)$this->getData(self::POST_ID);
    }

    public function setPostId(int $postId): CommentInterface
    {
        return $this->setData(self::POST_ID, $postId);
    }

    public function getParentId(): int
    {
        return (int)$this->getData(self::PARENT_ID);
    }

    public function setParentId(int $parentId): CommentInterface
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    public function getStatus(): int
    {
        return (int)$this->getData(self::STATUS);
    }

    public function setStatus(int $status): CommentInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getAuthorNickname(): ?string
    {
        $v = $this->getData(self::AUTHOR_NICKNAME);
        return $v === null ? null : (string)$v;
    }

    public function setAuthorNickname(?string $nickname): CommentInterface
    {
        return $this->setData(self::AUTHOR_NICKNAME, $nickname);
    }

    public function getAuthorEmail(): ?string
    {
        $v = $this->getData(self::AUTHOR_EMAIL);
        return $v === null ? null : (string)$v;
    }

    public function setAuthorEmail(?string $email): CommentInterface
    {
        return $this->setData(self::AUTHOR_EMAIL, $email);
    }

    public function getText(): ?string
    {
        $v = $this->getData(self::TEXT);
        return $v === null ? null : (string)$v;
    }

    public function setText(string $text): CommentInterface
    {
        return $this->setData(self::TEXT, $text);
    }

    public function getCreationTime(): ?string
    {
        $v = $this->getData(self::CREATION_TIME);
        return $v === null ? null : (string)$v;
    }
}
