<?php
/**
 * Delete a post.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Post;

use Etechflow\Blog\Api\PostRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::post';

    /** @var PostRepositoryInterface */
    private $postRepository;

    public function __construct(Context $context, PostRepositoryInterface $postRepository)
    {
        parent::__construct($context);
        $this->postRepository = $postRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $postId = (int)$this->getRequest()->getParam('post_id');
        if ($postId) {
            try {
                $this->postRepository->deleteById($postId);
                $this->messageManager->addSuccessMessage(__('The post has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['post_id' => $postId]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a post to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
