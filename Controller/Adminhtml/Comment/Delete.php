<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Comment;

use Etechflow\Blog\Api\CommentRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::comment';

    /** @var CommentRepositoryInterface */
    private $commentRepository;

    public function __construct(Context $context, CommentRepositoryInterface $commentRepository)
    {
        parent::__construct($context);
        $this->commentRepository = $commentRepository;
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('comment_id');
        if ($id) {
            try {
                $this->commentRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The comment has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $redirect->setPath('*/*/');
    }
}
