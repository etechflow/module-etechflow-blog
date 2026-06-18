<?php
/**
 * Service contract for a blog Category (supports nesting via parent_id/path).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api\Data;

interface CategoryInterface
{
    public const CATEGORY_ID      = 'category_id';
    public const PARENT_ID        = 'parent_id';
    public const PATH             = 'path';
    public const POSITION         = 'position';
    public const LEVEL            = 'level';
    public const IS_ACTIVE        = 'is_active';
    public const INCLUDE_IN_MENU  = 'include_in_menu';
    public const TITLE            = 'title';
    public const IDENTIFIER       = 'identifier';
    public const CONTENT          = 'content';
    public const CONTENT_HEADING  = 'content_heading';
    public const META_TITLE       = 'meta_title';
    public const META_KEYWORDS    = 'meta_keywords';
    public const META_DESCRIPTION = 'meta_description';
    public const DISPLAY_MODE     = 'display_mode';

    /** @return int|null */
    public function getId();

    /** @param int $id @return $this */
    public function setId($id);

    public function getTitle(): ?string;

    public function setTitle(string $title): self;

    public function getIdentifier(): ?string;

    public function setIdentifier(string $identifier): self;

    /** @return int */
    public function getParentId(): int;

    /** @param int $parentId @return $this */
    public function setParentId(int $parentId): self;

    public function getIsActive(): bool;

    public function setIsActive(bool $isActive): self;

    public function getIncludeInMenu(): bool;

    public function setIncludeInMenu(bool $include): self;

    public function getContent(): ?string;

    public function setContent(?string $content): self;

    public function getMetaTitle(): ?string;

    public function setMetaTitle(?string $metaTitle): self;

    public function getMetaDescription(): ?string;

    public function setMetaDescription(?string $metaDescription): self;
}
