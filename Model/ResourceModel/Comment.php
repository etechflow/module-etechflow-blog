<?php
/**
 * Comment resource model.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Comment extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('etechflow_blog_comment', 'comment_id');
    }
}
