<?php
/**
 * Blog tag page (lists posts with the tag).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Tag;

use Etechflow\Blog\Api\TagRepositoryInterface;
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
    /** @var TagRepositoryInterface */
    private $tagRepository;
    /** @var PageContext */
    private $pageContext;
    /** @var RequestInterface */
    private $request;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        PageFactory $pageFactory,
        ForwardFactory $forwardFactory,
        TagRepositoryInterface $tagRepository,
        PageContext $pageContext,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->pageFactory = $pageFactory;
        $this->forwardFactory = $forwardFactory;
        $this->tagRepository = $tagRepository;
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
            $tag = $this->tagRepository->getByIdentifier($identifier, $storeId);
        } catch (NoSuchEntityException $e) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        if (!$tag->getIsActive()) {
            return $this->forwardFactory->create()->forward('noroute');
        }

        $this->pageContext->setTag($tag);
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set((string)($tag->getData('meta_title') ?: $tag->getTitle()));
        return $page;
    }
}
