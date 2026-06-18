<?php
/**
 * Blog category page (lists posts in the category).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Category;

use Etechflow\Blog\Api\CategoryRepositoryInterface;
use Etechflow\Blog\Model\PageContext;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

class View implements HttpGetActionInterface
{
    /** @var PageFactory */
    private $pageFactory;
    /** @var ForwardFactory */
    private $forwardFactory;
    /** @var CategoryRepositoryInterface */
    private $categoryRepository;
    /** @var PageContext */
    private $pageContext;
    /** @var RequestInterface */
    private $request;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        PageFactory $pageFactory,
        ForwardFactory $forwardFactory,
        CategoryRepositoryInterface $categoryRepository,
        PageContext $pageContext,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->pageFactory = $pageFactory;
        $this->forwardFactory = $forwardFactory;
        $this->categoryRepository = $categoryRepository;
        $this->pageContext = $pageContext;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $identifier = (string)$this->request->getParam('identifier');
        if ($identifier === '') {
            return $this->forwardFactory->create()->forward('noroute');
        }
        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
            $category = $this->categoryRepository->getByIdentifier($identifier, $storeId);
        } catch (NoSuchEntityException $e) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        if (!$category->getIsActive()) {
            return $this->forwardFactory->create()->forward('noroute');
        }

        $this->pageContext->setCategory($category);
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set((string)($category->getData('meta_title') ?: $category->getTitle()));
        if ($category->getData('meta_description')) {
            $page->getConfig()->setMetadata('description', (string)$category->getData('meta_description'));
        }
        return $page;
    }
}
