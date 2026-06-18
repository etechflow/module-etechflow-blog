<?php
/**
 * Blog archive page (posts from a given year / month).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Archive;

use Etechflow\Blog\Model\PageContext;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;

class View implements HttpGetActionInterface
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
        $year = preg_replace('/\D/', '', (string)$this->request->getParam('year'));
        $month = preg_replace('/\D/', '', (string)$this->request->getParam('month'));
        if ($year === '') {
            return $this->forwardFactory->create()->forward('noroute');
        }

        $this->pageContext->setArchive($year, $month !== '' ? $month : null);
        $page = $this->pageFactory->create();
        $label = $month !== ''
            ? date('F Y', mktime(0, 0, 0, (int)$month, 1, (int)$year))
            : $year;
        $page->getConfig()->getTitle()->set(__('Blog Archive — %1', $label));
        return $page;
    }
}
