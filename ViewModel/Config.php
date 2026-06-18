<?php
/**
 * Template-facing settings + URL helper. Injected into blocks as a view model
 * so .phtml files never touch ObjectManager.
 */
declare(strict_types=1);

namespace Etechflow\Blog\ViewModel;

use Etechflow\Blog\Model\Url;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config implements ArgumentInterface
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;
    /** @var Url */
    private $url;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Url $url,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
        $this->storeManager = $storeManager;
    }

    /**
     * Resolve an image path to a usable URL. Accepts absolute URLs, root-relative
     * paths, or paths stored under pub/media.
     */
    public function imageUrl(?string $path): string
    {
        $path = (string)$path;
        if ($path === '') {
            return '';
        }
        if (preg_match('#^(https?:)?//#', $path) || strpos($path, '/') === 0) {
            return $path;
        }
        $media = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return rtrim($media, '/') . '/' . ltrim($path, '/');
    }

    public function url(): Url
    {
        return $this->url;
    }

    public function isFlag(string $path): bool
    {
        return $this->scopeConfig->isSetFlag('etechflow_blog/' . $path, ScopeInterface::SCOPE_STORE);
    }

    public function getValue(string $path)
    {
        return $this->scopeConfig->getValue('etechflow_blog/' . $path, ScopeInterface::SCOPE_STORE);
    }

    public function getInt(string $path, int $default = 0): int
    {
        $v = $this->getValue($path);
        return $v === null || $v === '' ? $default : (int)$v;
    }

    public function getString(string $path, string $default = ''): string
    {
        $v = $this->getValue($path);
        return $v === null || $v === '' ? $default : (string)$v;
    }

    /** Convenience accessors used widely by templates */
    public function blogTitle(): string
    {
        return $this->getString('general/title', 'Blog');
    }

    public function gridColumns(): int
    {
        return max(1, $this->getInt('design/grid_columns', 3));
    }

    public function showFeatured(): bool
    {
        return $this->isFlag('design/featured_post');
    }

    /**
     * Lazy-loading of below-the-fold images is always on (the Performance
     * config group was removed; the feature is no longer toggleable).
     */
    public function lazyImages(): bool
    {
        return true;
    }

    public function socialNetworks(): array
    {
        $raw = $this->getString('social_share/networks', 'facebook,twitter,linkedin,whatsapp,email');
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }
}
