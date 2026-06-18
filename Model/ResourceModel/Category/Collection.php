<?php
/**
 * Category collection with active/menu/store helpers and post counts.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'category_id';

    protected function _construct()
    {
        $this->_init(
            \Etechflow\Blog\Model\Category::class,
            \Etechflow\Blog\Model\ResourceModel\Category::class
        );
    }

    public function addActiveFilter(): self
    {
        $this->addFieldToFilter('is_active', \Etechflow\Blog\Model\Category::STATUS_ENABLED);
        return $this;
    }

    public function addMenuFilter(): self
    {
        $this->addFieldToFilter('include_in_menu', 1);
        return $this;
    }

    public function addStoreFilter(int $storeId): self
    {
        $this->getSelect()->join(
            ['store_table' => $this->getTable('etechflow_blog_category_store')],
            'main_table.category_id = store_table.category_id',
            []
        )->where('store_table.store_id IN (?)', [0, $storeId])
         ->group('main_table.category_id');
        return $this;
    }

    /**
     * Add the count of published posts in each category (for sidebar badges).
     */
    public function addPostsCount(): self
    {
        $this->getSelect()->joinLeft(
            ['pc' => $this->getTable('etechflow_blog_post_category')],
            'main_table.category_id = pc.category_id',
            ['posts_count' => 'COUNT(pc.post_id)']
        )->group('main_table.category_id');
        return $this;
    }
}
