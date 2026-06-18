<?php
/**
 * Central permalink builder. Produces clean, SEO-friendly URLs:
 *   /blog                      blog home
 *   /blog/{identifier}         a post
 *   /blog/category/{id}        a category
 *   /blog/tag/{id}             a tag
 *   /blog/author/{id}          an author
 *   /blog/archive/{Y}/{m}      a date archive
 *   /blog/search/{query}       search results
 *   /blog/rss                  RSS feed
 * The base route ("blog") is configurable via etechflow_blog/general/route.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Url
{
    public const XML_PATH_ROUTE = 'etechflow_blog/general/route';

    /** @var ScopeConfigInterface */
    private $scopeConfig;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getRoute(): string
    {
        $route = (string)$this->scopeConfig->getValue(self::XML_PATH_ROUTE, ScopeInterface::SCOPE_STORE);
        return $route !== '' ? trim($route, '/') : 'blog';
    }

    private function base(): string
    {
        return rtrim($this->storeManager->getStore()->getBaseUrl(), '/') . '/' . $this->getRoute() . '/';
    }

    public function getBaseUrl(): string
    {
        return $this->base();
    }

    public function getPostUrl($post): string
    {
        return $this->base() . $post->getIdentifier();
    }

    public function getCategoryUrl($category): string
    {
        return $this->base() . 'category/' . $category->getIdentifier();
    }

    public function getTagUrl($tag): string
    {
        return $this->base() . 'tag/' . $tag->getIdentifier();
    }

    public function getAuthorUrl($author): string
    {
        return $this->base() . 'author/' . $author->getIdentifier();
    }

    public function getArchiveUrl(string $year, string $month): string
    {
        return $this->base() . 'archive/' . $year . '/' . $month;
    }

    public function getSearchUrl(string $query = ''): string
    {
        return $this->base() . 'search' . ($query !== '' ? '/' . rawurlencode($query) : '');
    }

    public function getRssUrl(): string
    {
        return $this->base() . 'rss';
    }
}
