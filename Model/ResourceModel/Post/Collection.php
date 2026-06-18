<?php
/**
 * Post collection. Adds storefront-friendly helpers: only-active filtering,
 * per-store visibility, published-only, and recency ordering — all index-backed
 * for speed.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\ResourceModel\Post;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'post_id';

    protected function _construct()
    {
        $this->_init(
            \Etechflow\Blog\Model\Post::class,
            \Etechflow\Blog\Model\ResourceModel\Post::class
        );
    }

    /**
     * Only enabled posts.
     */
    public function addActiveFilter(): self
    {
        $this->addFieldToFilter('is_active', \Etechflow\Blog\Model\Post::STATUS_ENABLED);
        return $this;
    }

    /**
     * Only posts whose publish_time is in the past (scheduling support).
     */
    public function addPublishedFilter(DateTime $date): self
    {
        $this->addFieldToFilter('publish_time', ['lteq' => $date->gmtDate()]);
        return $this;
    }

    /**
     * Restrict to posts visible on the given store view.
     */
    public function addStoreFilter(int $storeId): self
    {
        $this->getSelect()->join(
            ['store_table' => $this->getTable('etechflow_blog_post_store')],
            'main_table.post_id = store_table.post_id',
            []
        )->where('store_table.store_id IN (?)', [0, $storeId])
         ->group('main_table.post_id');
        return $this;
    }

    /**
     * Newest first.
     */
    public function setRecentOrder(): self
    {
        $this->setOrder('publish_time', self::SORT_ORDER_DESC);
        return $this;
    }

    /**
     * Most viewed first (powers the "popular posts" widget).
     */
    public function setPopularOrder(): self
    {
        $this->setOrder('views_count', self::SORT_ORDER_DESC);
        return $this;
    }
}
