<?php
/**
 * Blog author page (lists the author's posts + their bio).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Author;

use Etechflow\Blog\Api\AuthorRepositoryInterface;
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
    /** @var AuthorRepositoryInterface */
    private $authorRepository;
    /** @var PageContext */
    private $pageContext;
    /** @var RequestInterface */
    private $request;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        PageFactory $pageFactory,
        ForwardFactory $forwardFactory,
        AuthorRepositoryInterface $authorRepository,
        PageContext $pageContext,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->pageFactory = $pageFactory;
        $this->forwardFactory = $forwardFactory;
        $this->authorRepository = $authorRepository;
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
            $author = $this->authorRepository->getByIdentifier($identifier, $storeId);
        } catch (NoSuchEntityException $e) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        if (!$author->getIsActive()) {
            return $this->forwardFactory->create()->forward('noroute');
        }

        $this->pageContext->setAuthor($author);
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set((string)($author->getData('meta_title') ?: $author->getTitle()));
        return $page;
    }
}
