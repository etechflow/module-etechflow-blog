<?php
/**
 * Listing block — powers the blog home plus category/tag/author/archive/search
 * pages. It reads the current PageContext to decide which posts to show and
 * builds an index-backed, paginated, store-aware collection.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Block;

use Etechflow\Blog\Model\PageContext;
use Etechflow\Blog\Model\ResourceModel\Post\Collection;
use Etechflow\Blog\Model\ResourceModel\Post\CollectionFactory;
use Etechflow\Blog\Model\Url;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class PostList extends Template
{
    /** @var PageContext */
    private $pageContext;
    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var Url */
    private $url;
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var DateTime */
    private $date;
    /** @var Collection|null */
    private $collection;

    public function __construct(
        Context $context,
        PageContext $pageContext,
        CollectionFactory $collectionFactory,
        Url $url,
        StoreManagerInterface $storeManager,
        DateTime $date,
        array $data = []
    ) {
        $this->pageContext = $pageContext;
        $this->collectionFactory = $collectionFactory;
        $this->url = $url;
        $this->storeManager = $storeManager;
        $this->date = $date;
        parent::__construct($context, $data);
    }

    public function getPageContext(): PageContext
    {
        return $this->pageContext;
    }

    public function getUrlModel(): Url
    {
        return $this->url;
    }

    public function getPostCollection(): Collection
    {
        if ($this->collection !== null) {
            return $this->collection;
        }
        $storeId = (int)$this->storeManager->getStore()->getId();
        $collection = $this->collectionFactory->create();
        $collection->addActiveFilter()
            ->addStoreFilter($storeId)
            ->addFieldToFilter('publish_time', ['lteq' => $this->date->gmtDate()]);

        $ctx = $this->pageContext;
        switch ($ctx->getPageType()) {
            case PageContext::TYPE_CATEGORY:
                if ($ctx->getCategory()) {
                    $collection->getSelect()->join(
                        ['etf_pc' => $collection->getTable('etechflow_blog_post_category')],
                        'main_table.post_id = etf_pc.post_id',
                        []
                    )->where('etf_pc.category_id = ?', (int)$ctx->getCategory()->getId());
                }
                break;
            case PageContext::TYPE_TAG:
                if ($ctx->getTag()) {
                    $collection->getSelect()->join(
                        ['etf_pt' => $collection->getTable('etechflow_blog_post_tag')],
                        'main_table.post_id = etf_pt.post_id',
                        []
                    )->where('etf_pt.tag_id = ?', (int)$ctx->getTag()->getId());
                }
                break;
            case PageContext::TYPE_AUTHOR:
                if ($ctx->getAuthor()) {
                    $collection->addFieldToFilter('author_id', (int)$ctx->getAuthor()->getId());
                }
                break;
            case PageContext::TYPE_ARCHIVE:
                $year = $ctx->getArchiveYear();
                $month = $ctx->getArchiveMonth();
                if ($year) {
                    $start = sprintf('%04d-%02d-01 00:00:00', (int)$year, (int)($month ?: 1));
                    $end = $month
                        ? date('Y-m-t 23:59:59', strtotime($start))
                        : sprintf('%04d-12-31 23:59:59', (int)$year);
                    $collection->addFieldToFilter('publish_time', ['from' => $start, 'to' => $end]);
                }
                break;
            case PageContext::TYPE_SEARCH:
                $q = trim($ctx->getSearchQuery());
                if ($q !== '') {
                    $like = '%' . $q . '%';
                    $collection->addFieldToFilter(
                        ['title', 'short_content', 'content'],
                        [['like' => $like], ['like' => $like], ['like' => $like]]
                    );
                }
                break;
        }

        $collection->setRecentOrder();
        $collection->setPageSize($this->getPageSize());
        $collection->setCurPage($this->getCurrentPage());
        $this->collection = $collection;
        return $collection;
    }

    public function getPageSize(): int
    {
        $size = (int)$this->_scopeConfig->getValue('etechflow_blog/general/posts_per_page', ScopeInterface::SCOPE_STORE);
        return $size > 0 ? $size : 9;
    }

    public function getCurrentPage(): int
    {
        return max(1, (int)$this->getRequest()->getParam('p', 1));
    }

    public function getLastPage(): int
    {
        return (int)$this->getPostCollection()->getLastPageNumber();
    }

    public function getCurrentListUrl(): string
    {
        $ctx = $this->pageContext;
        switch ($ctx->getPageType()) {
            case PageContext::TYPE_CATEGORY:
                return $ctx->getCategory() ? $this->url->getCategoryUrl($ctx->getCategory()) : $this->url->getBaseUrl();
            case PageContext::TYPE_TAG:
                return $ctx->getTag() ? $this->url->getTagUrl($ctx->getTag()) : $this->url->getBaseUrl();
            case PageContext::TYPE_AUTHOR:
                return $ctx->getAuthor() ? $this->url->getAuthorUrl($ctx->getAuthor()) : $this->url->getBaseUrl();
            case PageContext::TYPE_SEARCH:
                return $this->url->getSearchUrl($ctx->getSearchQuery());
            default:
                return $this->url->getBaseUrl();
        }
    }

    public function getPageUrl(int $page): string
    {
        $base = $this->getCurrentListUrl();
        return $page > 1 ? $base . (strpos($base, '?') !== false ? '&' : '?') . 'p=' . $page : $base;
    }

    public function getPostUrl($post): string
    {
        return $this->url->getPostUrl($post);
    }

    public function getThumbnail($post): string
    {
        return (string)($post->getData('featured_list_img') ?: $post->getData('featured_img'));
    }

    public function getExcerpt($post, int $limit = 0): string
    {
        $text = (string)$post->getData('short_content');
        if ($text === '') {
            $text = trim(preg_replace('/\s+/', ' ', strip_tags((string)$post->getData('content'))));
        }
        if ($limit <= 0) {
            $limit = (int)$this->_scopeConfig->getValue('etechflow_blog/general/short_content_limit', ScopeInterface::SCOPE_STORE) ?: 200;
        }
        if (mb_strlen($text) > $limit) {
            $text = mb_substr($text, 0, $limit) . '…';
        }
        return $text;
    }

    public function getPostDate($post): string
    {
        $time = $post->getData('publish_time') ?: $post->getData('creation_time');
        return $time ? $this->formatDate($time, \IntlDateFormatter::MEDIUM) : '';
    }

    public function getListTitle(): string
    {
        $ctx = $this->pageContext;
        switch ($ctx->getPageType()) {
            case PageContext::TYPE_CATEGORY:
                return $ctx->getCategory() ? (string)$ctx->getCategory()->getData('title') : '';
            case PageContext::TYPE_TAG:
                return $ctx->getTag() ? __('Tag: %1', $ctx->getTag()->getData('title'))->render() : '';
            case PageContext::TYPE_AUTHOR:
                return $ctx->getAuthor() ? (string)$ctx->getAuthor()->getData('title') : '';
            case PageContext::TYPE_SEARCH:
                return __('Search results for "%1"', $ctx->getSearchQuery())->render();
            case PageContext::TYPE_ARCHIVE:
                return __('Archive')->render();
            default:
                return (string)($this->_scopeConfig->getValue('etechflow_blog/general/title', ScopeInterface::SCOPE_STORE) ?: 'Blog');
        }
    }
}
