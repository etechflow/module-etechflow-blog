<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Author;

use Etechflow\Blog\Api\AuthorRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::author';

    /** @var PageFactory */
    private $resultPageFactory;
    /** @var AuthorRepositoryInterface */
    private $authorRepository;

    public function __construct(Context $context, PageFactory $resultPageFactory, AuthorRepositoryInterface $authorRepository)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->authorRepository = $authorRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('author_id');
        if ($id) {
            try {
                $this->authorRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This author no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }
        $page = $this->resultPageFactory->create();
        $page->setActiveMenu('Etechflow_Blog::author');
        $page->getConfig()->getTitle()->prepend($id ? __('Edit Author') : __('New Author'));
        return $page;
    }
}
