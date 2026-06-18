<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Category;

use Etechflow\Blog\Api\CategoryRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::category';

    /** @var CategoryRepositoryInterface */
    private $categoryRepository;

    public function __construct(Context $context, CategoryRepositoryInterface $categoryRepository)
    {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('category_id');
        if ($id) {
            try {
                $this->categoryRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The category has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $redirect->setPath('*/*/edit', ['category_id' => $id]);
            }
        }
        return $redirect->setPath('*/*/');
    }
}
