<?php
/**
 * Service contract for a blog Tag.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api\Data;

interface TagInterface
{
    public const TAG_ID           = 'tag_id';
    public const IS_ACTIVE        = 'is_active';
    public const TITLE            = 'title';
    public const IDENTIFIER       = 'identifier';
    public const CONTENT          = 'content';
    public const META_TITLE       = 'meta_title';
    public const META_KEYWORDS    = 'meta_keywords';
    public const META_DESCRIPTION = 'meta_description';

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

    public function getMetaTitle(): ?string;

    public function setMetaTitle(?string $metaTitle): self;

    public function getMetaDescription(): ?string;

    public function setMetaDescription(?string $metaDescription): self;
}
