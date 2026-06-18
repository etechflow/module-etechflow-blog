<?php
/**
 * Service contract for a blog Comment (supports nesting + moderation status).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api\Data;

interface CommentInterface
{
    public const COMMENT_ID      = 'comment_id';
    public const POST_ID         = 'post_id';
    public const PARENT_ID       = 'parent_id';
    public const CUSTOMER_ID     = 'customer_id';
    public const STORE_ID        = 'store_id';
    public const STATUS          = 'status';
    public const AUTHOR_TYPE     = 'author_type';
    public const AUTHOR_NICKNAME = 'author_nickname';
    public const AUTHOR_EMAIL    = 'author_email';
    public const TEXT            = 'text';
    public const ADMIN_REPLY     = 'admin_reply';
    public const CREATION_TIME   = 'creation_time';

    /** @return int|null */
    public function getId();

    /** @param int $id @return $this */
    public function setId($id);

    public function getPostId(): int;

    public function setPostId(int $postId): self;

    public function getParentId(): int;

    public function setParentId(int $parentId): self;

    public function getStatus(): int;

    public function setStatus(int $status): self;

    public function getAuthorNickname(): ?string;

    public function setAuthorNickname(?string $nickname): self;

    public function getAuthorEmail(): ?string;

    public function setAuthorEmail(?string $email): self;

    public function getText(): ?string;

    public function setText(string $text): self;

    public function getCreationTime(): ?string;
}
