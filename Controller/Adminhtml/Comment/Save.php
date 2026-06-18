<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Comment;

use Etechflow\Blog\Api\CommentRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action implements HttpPostActionInterface
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
        $data = $this->getRequest()->getPostValue();
        $id = (int)($data['comment_id'] ?? 0);
        if (!$id) {
            return $redirect->setPath('*/*/');
        }
        try {
            $comment = $this->commentRepository->getById($id);
            if (isset($data['status'])) {
                $comment->setStatus((int)$data['status']);
            }
            if (isset($data['text'])) {
                $comment->setText((string)$data['text']);
            }
            if (isset($data['admin_reply'])) {
                $comment->setData('admin_reply', (string)$data['admin_reply']);
            }
            $this->commentRepository->save($comment);
            $this->messageManager->addSuccessMessage(__('The comment has been saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $redirect->setPath('*/*/edit', ['comment_id' => $id]);
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This comment no longer exists.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $redirect->setPath('*/*/edit', ['comment_id' => $id]);
        }
        return $redirect->setPath('*/*/');
    }
}
