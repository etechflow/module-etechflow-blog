<?php
/**
 * Single post page. Loads the post by slug, registers it for the blocks,
 * bumps the view counter, and sets the page title/meta.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Post;

use Etechflow\Blog\Api\PostRepositoryInterface;
use Etechflow\Blog\Model\PageContext;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class View implements HttpGetActionInterface
{
    /** @var PageFactory */
    private $pageFactory;
    /** @var ForwardFactory */
    private $forwardFactory;
    /** @var PostRepositoryInterface */
    private $postRepository;
    /** @var PageContext */
    private $pageContext;
    /** @var RequestInterface */
    private $request;
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var ScopeConfigInterface */
    private $scopeConfig;
    /** @var ResourceConnection */
    private $resource;

    public function __construct(
        PageFactory $pageFactory,
        ForwardFactory $forwardFactory,
        PostRepositoryInterface $postRepository,
        PageContext $pageContext,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $resource
    ) {
        $this->pageFactory = $pageFactory;
        $this->forwardFactory = $forwardFactory;
        $this->postRepository = $postRepository;
        $this->pageContext = $pageContext;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->resource = $resource;
    }

    public function execute()
    {
        if (!$this->scopeConfig->isSetFlag('etechflow_blog/general/enabled', ScopeInterface::SCOPE_STORE)) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        $identifier = (string)$this->request->getParam('identifier');
        if ($identifier === '') {
            return $this->forwardFactory->create()->forward('noroute');
        }
        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
            $post = $this->postRepository->getByIdentifier($identifier, $storeId);
        } catch (NoSuchEntityException $e) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        if (!$post->getIsActive()) {
            return $this->forwardFactory->create()->forward('noroute');
        }

        $this->pageContext->setPost($post);
        $this->bumpViews((int)$post->getId());

        $page = $this->pageFactory->create();
        $title = (string)($post->getData('meta_title') ?: $post->getTitle());
        $page->getConfig()->getTitle()->set($title);
        if ($post->getData('meta_description')) {
            $page->getConfig()->setMetadata('description', (string)$post->getData('meta_description'));
        }
        if ($post->getData('meta_keywords')) {
            $page->getConfig()->setMetadata('keywords', (string)$post->getData('meta_keywords'));
        }
        return $page;
    }

    private function bumpViews(int $postId): void
    {
        if (!$this->scopeConfig->isSetFlag('etechflow_blog/post_view/views_count', ScopeInterface::SCOPE_STORE)) {
            return;
        }
        try {
            $connection = $this->resource->getConnection();
            $table = $this->resource->getTableName('etechflow_blog_post');
            $connection->update($table, ['views_count' => new \Zend_Db_Expr('views_count + 1')], ['post_id = ?' => $postId]);
        } catch (\Exception $e) {
            // Non-critical — never break the page over a view count.
        }
    }
}
