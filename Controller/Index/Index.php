<?php
/**
 * Blog home (listing of all posts).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;

class Index implements HttpGetActionInterface
{
    /** @var PageFactory */
    private $pageFactory;
    /** @var ForwardFactory */
    private $forwardFactory;
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        PageFactory $pageFactory,
        ForwardFactory $forwardFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->pageFactory = $pageFactory;
        $this->forwardFactory = $forwardFactory;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        if (!$this->scopeConfig->isSetFlag('etechflow_blog/general/enabled', ScopeInterface::SCOPE_STORE)) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        $page = $this->pageFactory->create();
        $title = (string)$this->scopeConfig->getValue('etechflow_blog/general/title', ScopeInterface::SCOPE_STORE) ?: 'Blog';
        $page->getConfig()->getTitle()->set($title);
        return $page;
    }
}
