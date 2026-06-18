<?php
/**
 * Sidebar widgets block: search, categories (with counts), recent posts,
 * popular posts, archive (by month), tag cloud, and the RSS link. Each widget
 * respects its config toggle.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Block;

use Etechflow\Blog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Etechflow\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Etechflow\Blog\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;
use Etechflow\Blog\Model\Url;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Sidebar extends Template
{
    /** @var CategoryCollectionFactory */
    private $categoryCollectionFactory;
    /** @var PostCollectionFactory */
    private $postCollectionFactory;
    /** @var TagCollectionFactory */
    private $tagCollectionFactory;
    /** @var Url */
    private $url;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        Context $context,
        CategoryCollectionFactory $categoryCollectionFactory,
        PostCollectionFactory $postCollectionFactory,
        TagCollectionFactory $tagCollectionFactory,
        Url $url,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->url = $url;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getUrlModel(): Url
    {
        return $this->url;
    }

    public function show(string $widget): bool
    {
        return $this->_scopeConfig->isSetFlag('etechflow_blog/sidebar/enabled', ScopeInterface::SCOPE_STORE)
            && $this->_scopeConfig->isSetFlag('etechflow_blog/sidebar/' . $widget, ScopeInterface::SCOPE_STORE);
    }

    public function getCategories(): array
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addActiveFilter()->addPostsCount()->setOrder('title', 'ASC');
        return $collection->getItems();
    }

    public function getRecentPosts(): array
    {
        $count = (int)$this->_scopeConfig->getValue('etechflow_blog/sidebar/recent_posts_count', ScopeInterface::SCOPE_STORE) ?: 5;
        $collection = $this->postCollectionFactory->create();
        $collection->addActiveFilter()->setRecentOrder()->setPageSize($count);
        return $collection->getItems();
    }

    public function getPopularPosts(): array
    {
        $count = (int)$this->_scopeConfig->getValue('etechflow_blog/sidebar/popular_posts_count', ScopeInterface::SCOPE_STORE) ?: 5;
        $collection = $this->postCollectionFactory->create();
        $collection->addActiveFilter()->setPopularOrder()->setPageSize($count);
        return $collection->getItems();
    }

    public function getTags(): array
    {
        $collection = $this->tagCollectionFactory->create();
        $collection->addActiveFilter()->addPostsCount()->setOrder('title', 'ASC')->setPageSize(40);
        return $collection->getItems();
    }

    /** @return array of ['label','url','count'] */
    public function getArchiveMonths(): array
    {
        $collection = $this->postCollectionFactory->create();
        $connection = $collection->getConnection();
        $select = $connection->select()
            ->from(['p' => $collection->getMainTable()], [
                'period' => "DATE_FORMAT(p.publish_time, '%Y-%m')",
                'cnt' => 'COUNT(*)',
            ])
            ->where('p.is_active = ?', 1)
            ->where('p.publish_time IS NOT NULL')
            ->group('period')
            ->order('period DESC')
            ->limit(24);

        $rows = $connection->fetchAll($select);
        $months = [];
        foreach ($rows as $row) {
            if (empty($row['period'])) {
                continue;
            }
            [$year, $month] = explode('-', $row['period']);
            $months[] = [
                'label' => date('F Y', mktime(0, 0, 0, (int)$month, 1, (int)$year)),
                'url' => $this->url->getArchiveUrl($year, $month),
                'count' => (int)$row['cnt'],
            ];
        }
        return $months;
    }

    public function getThumbnail($post): string
    {
        return (string)($post->getData('featured_list_img') ?: $post->getData('featured_img'));
    }
}
