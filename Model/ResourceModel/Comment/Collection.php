<?php
/**
 * Comment collection with post/status/approved helpers.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\ResourceModel\Comment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'comment_id';

    protected function _construct()
    {
        $this->_init(
            \Etechflow\Blog\Model\Comment::class,
            \Etechflow\Blog\Model\ResourceModel\Comment::class
        );
    }

    public function addPostFilter(int $postId): self
    {
        $this->addFieldToFilter('post_id', $postId);
        return $this;
    }

    public function addApprovedFilter(): self
    {
        $this->addFieldToFilter('status', \Etechflow\Blog\Model\Comment::STATUS_APPROVED);
        return $this;
    }

    public function setChronologicalOrder(): self
    {
        $this->setOrder('creation_time', self::SORT_ORDER_ASC);
        return $this;
    }
}
