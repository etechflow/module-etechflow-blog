<?php
/**
 * Service contract for a blog Post. Defines the stable public API so the post
 * data model can be safely consumed by GraphQL, REST and other modules.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Api\Data;

interface PostInterface
{
    public const POST_ID            = 'post_id';
    public const IS_ACTIVE          = 'is_active';
    public const TITLE              = 'title';
    public const IDENTIFIER         = 'identifier';
    public const CONTENT_HEADING    = 'content_heading';
    public const CONTENT            = 'content';
    public const SHORT_CONTENT      = 'short_content';
    public const META_TITLE         = 'meta_title';
    public const META_KEYWORDS      = 'meta_keywords';
    public const META_DESCRIPTION   = 'meta_description';
    public const META_ROBOTS        = 'meta_robots';
    public const FEATURED_IMG       = 'featured_img';
    public const FEATURED_LIST_IMG  = 'featured_list_img';
    public const OG_IMG             = 'og_img';
    public const MEDIA_GALLERY      = 'media_gallery';
    public const AUTHOR_ID          = 'author_id';
    public const IS_COMMENTS_ENABLED = 'is_comments_enabled';
    public const VIEWS_COUNT        = 'views_count';
    public const PUBLISH_TIME       = 'publish_time';
    public const CREATION_TIME      = 'creation_time';
    public const UPDATE_TIME        = 'update_time';
    public const BANNER_IMG_DESKTOP = 'banner_img_desktop';
    public const BANNER_IMG_TABLET  = 'banner_img_tablet';
    public const BANNER_IMG_MOBILE  = 'banner_img_mobile';

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

    public function getContentHeading(): ?string;

    public function setContentHeading(?string $heading): self;

    public function getContent(): ?string;

    public function setContent(?string $content): self;

    public function getShortContent(): ?string;

    public function setShortContent(?string $shortContent): self;

    public function getMetaTitle(): ?string;

    public function setMetaTitle(?string $metaTitle): self;

    public function getMetaKeywords(): ?string;

    public function setMetaKeywords(?string $metaKeywords): self;

    public function getMetaDescription(): ?string;

    public function setMetaDescription(?string $metaDescription): self;

    public function getFeaturedImg(): ?string;

    public function setFeaturedImg(?string $img): self;

    /** @return int|null */
    public function getAuthorId();

    /** @param int|null $authorId @return $this */
    public function setAuthorId($authorId): self;

    public function getViewsCount(): int;

    public function setViewsCount(int $views): self;

    public function getPublishTime(): ?string;

    public function setPublishTime(?string $publishTime): self;

    public function getCreationTime(): ?string;

    public function getUpdateTime(): ?string;
}
