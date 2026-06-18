<?php
/**
 * Tag collection with active/store helpers and a usage count for tag clouds.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\ResourceModel\Tag;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'tag_id';

    protected function _construct()
    {
        $this->_init(
            \Etechflow\Blog\Model\Tag::class,
            \Etechflow\Blog\Model\ResourceModel\Tag::class
        );
    }

    public function addActiveFilter(): self
    {
        $this->addFieldToFilter('is_active', \Etechflow\Blog\Model\Tag::STATUS_ENABLED);
        return $this;
    }

    public function addStoreFilter(int $storeId): self
    {
        $this->getSelect()->join(
            ['store_table' => $this->getTable('etechflow_blog_tag_store')],
            'main_table.tag_id = store_table.tag_id',
            []
        )->where('store_table.store_id IN (?)', [0, $storeId])
         ->group('main_table.tag_id');
        return $this;
    }

    /**
     * Add the number of posts using each tag (sizes tag-cloud entries).
     */
    public function addPostsCount(): self
    {
        $this->getSelect()->joinLeft(
            ['pt' => $this->getTable('etechflow_blog_post_tag')],
            'main_table.tag_id = pt.tag_id',
            ['posts_count' => 'COUNT(pt.post_id)']
        )->group('main_table.tag_id');
        return $this;
    }
}
