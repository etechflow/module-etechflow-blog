<?php
/**
 * "Add New Post" — forwards to the edit form.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;

class NewAction extends Action
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::post';

    /** @var ForwardFactory */
    private $resultForwardFactory;

    public function __construct(Context $context, ForwardFactory $resultForwardFactory)
    {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
