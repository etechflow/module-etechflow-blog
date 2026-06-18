<?php
/**
 * Category resource model. Auto-generates a slug and maintains the
 * materialized path/level for nesting.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Filter\FilterManager;

class Category extends AbstractDb
{
    use StoreAwareTrait;

    /** @var FilterManager */
    private $filter;

    public function __construct(
        Context $context,
        FilterManager $filter,
        $connectionName = null
    ) {
        $this->filter = $filter;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('etechflow_blog_category', 'category_id');
    }

    protected function _beforeSave(AbstractModel $object)
    {
        if (!$object->getData('identifier')) {
            $object->setData('identifier', $this->filter->translitUrl((string)$object->getData('title')));
        }
        $parentId = (int)$object->getData('parent_id');
        if ($parentId > 0) {
            $parentPath = (string)$this->getConnection()->fetchOne(
                $this->getConnection()->select()
                    ->from($this->getMainTable(), 'path')
                    ->where('category_id = ?', $parentId)
            );
            $object->setData('level', substr_count($parentPath, '/') + 1);
        } else {
            $object->setData('level', 0);
        }
        return parent::_beforeSave($object);
    }

    /**
     * Persist the path once the auto-increment id is known.
     */
    protected function _afterSave(AbstractModel $object)
    {
        if (!$object->getData('path') || strpos((string)$object->getData('path'), '/' . $object->getId()) === false) {
            $parentId = (int)$object->getData('parent_id');
            $prefix = '';
            if ($parentId > 0) {
                $prefix = (string)$this->getConnection()->fetchOne(
                    $this->getConnection()->select()
                        ->from($this->getMainTable(), 'path')
                        ->where('category_id = ?', $parentId)
                ) . '/';
            }
            $this->getConnection()->update(
                $this->getMainTable(),
                ['path' => $prefix . $object->getId()],
                ['category_id = ?' => (int)$object->getId()]
            );
        }
        $this->syncStores('etechflow_blog_category_store', 'category_id', (int)$object->getId(), $object->getData('store_id'));
        return parent::_afterSave($object);
    }

    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $object->setData('store_id', $this->readStores('etechflow_blog_category_store', 'category_id', (int)$object->getId()));
        }
        return parent::_afterLoad($object);
    }
}
