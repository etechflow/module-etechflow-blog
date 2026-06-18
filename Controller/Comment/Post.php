<?php
/**
 * Handles comment submissions. Respects the global "comments enabled",
 * "allow guest" and "moderation" settings; new comments are held as pending
 * when moderation is on. CSRF is enforced via the form key on the form.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Comment;

use Etechflow\Blog\Api\CommentRepositoryInterface;
use Etechflow\Blog\Model\Comment as CommentModel;
use Etechflow\Blog\Model\CommentFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Post implements HttpPostActionInterface
{
    /** @var RequestInterface */
    private $request;
    /** @var CommentFactory */
    private $commentFactory;
    /** @var CommentRepositoryInterface */
    private $commentRepository;
    /** @var ScopeConfigInterface */
    private $scopeConfig;
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var CustomerSession */
    private $customerSession;
    /** @var ManagerInterface */
    private $messageManager;
    /** @var RedirectFactory */
    private $redirectFactory;

    public function __construct(
        RequestInterface $request,
        CommentFactory $commentFactory,
        CommentRepositoryInterface $commentRepository,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        ManagerInterface $messageManager,
        RedirectFactory $redirectFactory
    ) {
        $this->request = $request;
        $this->commentFactory = $commentFactory;
        $this->commentRepository = $commentRepository;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
    }

    public function execute()
    {
        $redirect = $this->redirectFactory->create();
        $redirect->setRefererUrl();

        $postId = (int)$this->request->getParam('post_id');
        $text = trim((string)$this->request->getParam('text'));

        if (!$this->scopeConfig->isSetFlag('etechflow_blog/comments/enabled', ScopeInterface::SCOPE_STORE)) {
            return $redirect;
        }
        $isLoggedIn = $this->customerSession->isLoggedIn();
        if (!$isLoggedIn && !$this->scopeConfig->isSetFlag('etechflow_blog/comments/allow_guest', ScopeInterface::SCOPE_STORE)) {
            $this->messageManager->addErrorMessage(__('Please sign in to comment.'));
            return $redirect;
        }
        if (!$postId || $text === '') {
            $this->messageManager->addErrorMessage(__('Your comment cannot be empty.'));
            return $redirect;
        }

        $moderation = $this->scopeConfig->isSetFlag('etechflow_blog/comments/moderation', ScopeInterface::SCOPE_STORE);

        try {
            /** @var CommentModel $comment */
            $comment = $this->commentFactory->create();
            $comment->setPostId($postId);
            $comment->setParentId((int)$this->request->getParam('parent_id'));
            $comment->setText($text);
            $comment->setData('store_id', (int)$this->storeManager->getStore()->getId());
            $comment->setData('author_type', $isLoggedIn ? CommentModel::TYPE_CUSTOMER : CommentModel::TYPE_GUEST);
            $comment->setData('customer_id', $isLoggedIn ? (int)$this->customerSession->getCustomerId() : null);
            $comment->setAuthorNickname((string)$this->request->getParam('author_nickname'));
            $comment->setAuthorEmail((string)$this->request->getParam('author_email'));
            $comment->setStatus($moderation ? CommentModel::STATUS_PENDING : CommentModel::STATUS_APPROVED);
            $this->commentRepository->save($comment);

            $this->messageManager->addSuccessMessage(
                $moderation
                    ? __('Thanks! Your comment has been submitted and is awaiting approval.')
                    : __('Thanks for your comment!')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We could not save your comment. Please try again.'));
        }

        return $redirect;
    }
}
