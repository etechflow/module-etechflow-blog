<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::category';

    /** @var PageFactory */
    private $resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $page = $this->resultPageFactory->create();
        $page->setActiveMenu('Etechflow_Blog::category');
        $page->getConfig()->getTitle()->prepend(__('Blog Categories'));
        return $page;
    }
}
