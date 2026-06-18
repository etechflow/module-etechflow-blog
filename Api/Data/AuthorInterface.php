<?php
/**
 * Service contract for a blog Author.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api\Data;

interface AuthorInterface
{
    public const AUTHOR_ID        = 'author_id';
    public const IS_ACTIVE        = 'is_active';
    public const CUSTOMER_ID      = 'customer_id';
    public const IDENTIFIER       = 'identifier';
    public const TITLE            = 'title';
    public const FIRST_NAME       = 'first_name';
    public const LAST_NAME        = 'last_name';
    public const EMAIL            = 'email';
    public const IMAGE            = 'image';
    public const CONTENT          = 'content';
    public const META_TITLE       = 'meta_title';
    public const META_DESCRIPTION = 'meta_description';
    public const FACEBOOK         = 'facebook';
    public const TWITTER          = 'twitter';
    public const LINKEDIN         = 'linkedin';
    public const INSTAGRAM        = 'instagram';
    public const WEBSITE          = 'website';

    /** @return int|null */
    public function getId();

    /** @param int $id @return $this */
    public function setId($id);

    public function getTitle(): ?string;

    public function setTitle(string $title): self;

    public function getIdentifier(): ?string;

    public function setIdentifier(string $identifier): self;

    public function getIsActive(): bool;

    public function setIsActive(bool $isActive): self;

    public function getImage(): ?string;

    public function setImage(?string $image): self;

    public function getContent(): ?string;

    public function setContent(?string $content): self;

    public function getEmail(): ?string;

    public function setEmail(?string $email): self;
}
