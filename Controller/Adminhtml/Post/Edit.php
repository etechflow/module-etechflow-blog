<?php
/**
 * Post edit/create form page.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Post;

use Etechflow\Blog\Api\PostRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::post';

    /** @var PageFactory */
    private $resultPageFactory;
    /** @var PostRepositoryInterface */
    private $postRepository;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PostRepositoryInterface $postRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->postRepository = $postRepository;
    }

    public function execute()
    {
        $postId = (int)$this->getRequest()->getParam('post_id');
        if ($postId) {
            try {
                $this->postRepository->getById($postId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Etechflow_Blog::post');
        $resultPage->getConfig()->getTitle()->prepend($postId ? __('Edit Post') : __('New Post'));
        return $resultPage;
    }
}
