<?php
/**
 * Feeds blog posts and categories into Magento's native XML sitemap
 * (config-toggled via etechflow_blog/seo/include_in_sitemap).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\Sitemap;

use Etechflow\Blog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Etechflow\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Etechflow\Blog\Model\Url;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;
use Magento\Store\Model\ScopeInterface;

class ItemProvider implements ItemProviderInterface
{
    /** @var SitemapItemInterfaceFactory */
    private $itemFactory;
    /** @var PostCollectionFactory */
    private $postCollectionFactory;
    /** @var CategoryCollectionFactory */
    private $categoryCollectionFactory;
    /** @var Url */
    private $url;
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        SitemapItemInterfaceFactory $itemFactory,
        PostCollectionFactory $postCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Url $url,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->itemFactory = $itemFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->url = $url;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int $storeId
     * @return \Magento\Sitemap\Model\SitemapItemInterface[]
     */
    public function getItems($storeId): array
    {
        if (!$this->scopeConfig->isSetFlag('etechflow_blog/seo/include_in_sitemap', ScopeInterface::SCOPE_STORE, $storeId)) {
            return [];
        }
        $route = $this->url->getRoute();
        $items = [];

        $posts = $this->postCollectionFactory->create();
        $posts->addActiveFilter()->addStoreFilter((int)$storeId);
        foreach ($posts as $post) {
            $items[] = $this->itemFactory->create([
                'url' => $route . '/' . $post->getData('identifier'),
                'priority' => '0.6',
                'changeFrequency' => 'weekly',
                'updatedAt' => $post->getData('update_time'),
            ]);
        }

        $categories = $this->categoryCollectionFactory->create();
        $categories->addActiveFilter()->addStoreFilter((int)$storeId);
        foreach ($categories as $category) {
            $items[] = $this->itemFactory->create([
                'url' => $route . '/category/' . $category->getData('identifier'),
                'priority' => '0.4',
                'changeFrequency' => 'weekly',
                'updatedAt' => $category->getData('update_time'),
            ]);
        }

        return $items;
    }
}
