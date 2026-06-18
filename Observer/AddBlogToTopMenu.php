<?php
/**
 * Injects a "Blog" node into the storefront top menu (config-toggled).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Observer;

use Etechflow\Blog\Model\LicenseValidator;
use Etechflow\Blog\Model\Url;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class AddBlogToTopMenu implements ObserverInterface
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;
    /** @var Url */
    private $url;
    /** @var NodeFactory */
    private $nodeFactory;
    /** @var LicenseValidator */
    private $licenseValidator;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Url $url,
        NodeFactory $nodeFactory,
        LicenseValidator $licenseValidator
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
        $this->nodeFactory = $nodeFactory;
        $this->licenseValidator = $licenseValidator;
    }

    public function execute(Observer $observer)
    {
        if (!$this->scopeConfig->isSetFlag('etechflow_blog/general/enabled', ScopeInterface::SCOPE_STORE)
            || !$this->scopeConfig->isSetFlag('etechflow_blog/general/show_in_top_menu', ScopeInterface::SCOPE_STORE)
            || !$this->licenseValidator->isValid()
        ) {
            return;
        }
        $menu = $observer->getEvent()->getMenu();
        if (!$menu) {
            return;
        }
        $text = (string)$this->scopeConfig->getValue('etechflow_blog/general/top_menu_text', ScopeInterface::SCOPE_STORE) ?: 'Blog';

        $node = $this->nodeFactory->create([
            'data' => [
                'name' => $text,
                'id' => 'etechflow-blog',
                'url' => $this->url->getBaseUrl(),
            ],
            'idField' => 'id',
            'tree' => $menu->getTree(),
        ]);
        $menu->addChild($node);
    }
}
