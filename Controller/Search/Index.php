<?php
/**
 * Blog search results page.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Search;

use Etechflow\Blog\Model\PageContext;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;

class Index implements HttpGetActionInterface
{
    /** @var PageFactory */
    private $pageFactory;
    /** @var ForwardFactory */
    private $forwardFactory;
    /** @var PageContext */
    private $pageContext;
    /** @var RequestInterface */
    private $request;
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        PageFactory $pageFactory,
        ForwardFactory $forwardFactory,
        PageContext $pageContext,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->pageFactory = $pageFactory;
        $this->forwardFactory = $forwardFactory;
        $this->pageContext = $pageContext;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        if (!$this->scopeConfig->isSetFlag('etechflow_blog/general/enabled', ScopeInterface::SCOPE_STORE)) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        $query = trim((string)($this->request->getParam('q') ?: $this->request->getParam('query')));
        $this->pageContext->setSearchQuery($query);

        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set(__('Blog Search: %1', $query));
        return $page;
    }
}
