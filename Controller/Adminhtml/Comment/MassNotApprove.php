<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Comment;

use Etechflow\Blog\Model\Comment;
use Etechflow\Blog\Model\ResourceModel\Comment\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Ui\Component\MassAction\Filter;

class MassNotApprove extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::comment';

    /** @var Filter */
    private $filter;
    /** @var CollectionFactory */
    private $collectionFactory;

    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $count = 0;
        foreach ($collection as $comment) {
            $comment->setData('status', Comment::STATUS_NOT_APPROVED)->save();
            $count++;
        }
        $this->messageManager->addSuccessMessage(__('%1 comment(s) marked as not approved.', $count));
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
