<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Category;

use Etechflow\Blog\Api\CategoryRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::category';

    /** @var PageFactory */
    private $resultPageFactory;
    /** @var CategoryRepositoryInterface */
    private $categoryRepository;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('category_id');
        if ($id) {
            try {
                $this->categoryRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This category no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }
        $page = $this->resultPageFactory->create();
        $page->setActiveMenu('Etechflow_Blog::category');
        $page->getConfig()->getTitle()->prepend($id ? __('Edit Category') : __('New Category'));
        return $page;
    }
}
