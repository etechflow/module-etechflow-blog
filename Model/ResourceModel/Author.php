<?php
/**
 * Author resource model. Builds a slug from the display name (or first/last).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Filter\FilterManager;

class Author extends AbstractDb
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
        $this->_init('etechflow_blog_author', 'author_id');
    }

    protected function _beforeSave(AbstractModel $object)
    {
        if (!$object->getData('title')) {
            $name = trim($object->getData('first_name') . ' ' . $object->getData('last_name'));
            $object->setData('title', $name !== '' ? $name : 'Author');
        }
        if (!$object->getData('identifier')) {
            $object->setData('identifier', $this->filter->translitUrl((string)$object->getData('title')));
        }
        return parent::_beforeSave($object);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $this->syncStores('etechflow_blog_author_store', 'author_id', (int)$object->getId(), $object->getData('store_id'));
        return parent::_afterSave($object);
    }

    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $object->setData('store_id', $this->readStores('etechflow_blog_author_store', 'author_id', (int)$object->getId()));
        }
        return parent::_afterLoad($object);
    }
}
