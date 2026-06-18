<?php
/**
 * Post resource model. Handles DB persistence, auto-fills publish_time / a
 * unique URL identifier, and syncs the store / category / tag relation tables
 * on save (and loads them back on load so the admin form pre-fills).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Filter\FilterManager;

class Post extends AbstractDb
{
    /** @var DateTime */
    private $date;
    /** @var FilterManager */
    private $filter;

    public function __construct(
        Context $context,
        DateTime $date,
        FilterManager $filter,
        $connectionName = null
    ) {
        $this->date = $date;
        $this->filter = $filter;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('etechflow_blog_post', 'post_id');
    }

    protected function _beforeSave(AbstractModel $object)
    {
        if (!$object->getData('identifier')) {
            $object->setData('identifier', $this->filter->translitUrl((string)$object->getData('title')));
        }
        if ($object->isObjectNew() && !$object->getData('publish_time')) {
            $object->setData('publish_time', $this->date->gmtDate());
        }
        return parent::_beforeSave($object);
    }

    /**
     * Persist the many-to-many relations after the post row is saved.
     */
    protected function _afterSave(AbstractModel $object)
    {
        $postId = (int)$object->getId();
        $stores = $this->normalize($object->getData('store_id')) ?? [0];
        $this->syncLink('etechflow_blog_post_store', 'store_id', $postId, $stores, false);
        $this->syncLink('etechflow_blog_post_category', 'category_id', $postId, $this->normalize($object->getData('categories')));
        $this->syncLink('etechflow_blog_post_tag', 'tag_id', $postId, $this->normalize($object->getData('tags')));
        $this->syncLink('etechflow_blog_post_relatedproduct', 'related_id', $postId, $this->normalize($object->getData('related_products')));
        $this->syncLink('etechflow_blog_post_relatedpost', 'related_id', $postId, $this->normalize($object->getData('related_posts')));
        return parent::_afterSave($object);
    }

    /**
     * Load relations into the object so the admin form shows current selections.
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $postId = (int)$object->getId();
        if ($postId) {
            $object->setData('store_id', $this->readLink('etechflow_blog_post_store', 'store_id', $postId, false));
            $object->setData('categories', $this->readLink('etechflow_blog_post_category', 'category_id', $postId));
            $object->setData('tags', $this->readLink('etechflow_blog_post_tag', 'tag_id', $postId));
            $object->setData('related_products', $this->readLink('etechflow_blog_post_relatedproduct', 'related_id', $postId));
            $object->setData('related_posts', $this->readLink('etechflow_blog_post_relatedpost', 'related_id', $postId));
        }
        return parent::_afterLoad($object);
    }

    public function isIdentifierUsed(string $identifier, ?int $exceptId = null): bool
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), 'post_id')
            ->where('identifier = ?', $identifier);
        if ($exceptId !== null) {
            $select->where('post_id <> ?', $exceptId);
        }
        return (bool)$connection->fetchOne($select);
    }

    /**
     * Replace the link rows for a post in a link table.
     */
    private function syncLink(string $table, string $column, int $postId, ?array $ids, bool $usePosition = true): void
    {
        if ($ids === null) {
            return; // field not submitted — leave existing relations untouched
        }
        $connection = $this->getConnection();
        $tableName = $this->getTable($table);
        $connection->delete($tableName, ['post_id = ?' => $postId]);
        $position = 0;
        foreach (array_unique($ids) as $id) {
            $id = (int)$id;
            if ($id <= 0 && $table !== 'etechflow_blog_post_store') {
                continue;
            }
            $row = ['post_id' => $postId, $column => $id];
            if ($usePosition) {
                $row['position'] = $position++;
            }
            $connection->insert($tableName, $row);
        }
    }

    /**
     * @return int[]
     */
    private function readLink(string $table, string $column, int $postId, bool $usePosition = true): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable($table), $column)
            ->where('post_id = ?', $postId);
        if ($usePosition) {
            $select->order('position ' . \Magento\Framework\DB\Select::SQL_ASC);
        }
        return array_map('intval', $connection->fetchCol($select));
    }

    /**
     * Normalise a submitted value to an int array, or null when not submitted.
     */
    private function normalize($value): ?array
    {
        if ($value === null) {
            return null;
        }
        if (!is_array($value)) {
            $value = $value === '' ? [] : explode(',', (string)$value);
        }
        return array_filter(array_map('intval', $value), static function ($v) {
            return $v >= 0;
        });
    }
}
