<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Comment;

use Etechflow\Blog\Api\CommentRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::comment';

    /** @var PageFactory */
    private $resultPageFactory;
    /** @var CommentRepositoryInterface */
    private $commentRepository;

    public function __construct(Context $context, PageFactory $resultPageFactory, CommentRepositoryInterface $commentRepository)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->commentRepository = $commentRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('comment_id');
        try {
            $this->commentRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This comment no longer exists.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        $page = $this->resultPageFactory->create();
        $page->setActiveMenu('Etechflow_Blog::comment');
        $page->getConfig()->getTitle()->prepend(__('Edit Comment'));
        return $page;
    }
}
