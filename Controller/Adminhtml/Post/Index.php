<?php
/**
 * Posts grid page.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::post';

    /** @var PageFactory */
    private $resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Etechflow_Blog::post');
        $resultPage->getConfig()->getTitle()->prepend(__('Blog Posts'));
        return $resultPage;
    }
}
