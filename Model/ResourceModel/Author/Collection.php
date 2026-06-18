<?php
/**
 * Author collection with active/store helpers.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\ResourceModel\Author;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'author_id';

    protected function _construct()
    {
        $this->_init(
            \Etechflow\Blog\Model\Author::class,
            \Etechflow\Blog\Model\ResourceModel\Author::class
        );
    }

    public function addActiveFilter(): self
    {
        $this->addFieldToFilter('is_active', \Etechflow\Blog\Model\Author::STATUS_ENABLED);
        return $this;
    }

    public function addStoreFilter(int $storeId): self
    {
        $this->getSelect()->join(
            ['store_table' => $this->getTable('etechflow_blog_author_store')],
            'main_table.author_id = store_table.author_id',
            []
        )->where('store_table.store_id IN (?)', [0, $storeId])
         ->group('main_table.author_id');
        return $this;
    }
}
