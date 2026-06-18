<?php
/**
 * RSS 2.0 feed of the latest published posts at /blog/rss.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Rss;

use Etechflow\Blog\Model\ResourceModel\Post\CollectionFactory;
use Etechflow\Blog\Model\Url;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Feed implements HttpGetActionInterface
{
    /** @var ResultFactory */
    private $resultFactory;
    /** @var ForwardFactory */
    private $forwardFactory;
    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var Url */
    private $url;
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var ScopeConfigInterface */
    private $scopeConfig;
    /** @var DateTime */
    private $date;

    public function __construct(
        ResultFactory $resultFactory,
        ForwardFactory $forwardFactory,
        CollectionFactory $collectionFactory,
        Url $url,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        DateTime $date
    ) {
        $this->resultFactory = $resultFactory;
        $this->forwardFactory = $forwardFactory;
        $this->collectionFactory = $collectionFactory;
        $this->url = $url;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->date = $date;
    }

    public function execute()
    {
        if (!$this->scopeConfig->isSetFlag('etechflow_blog/rss/enabled', ScopeInterface::SCOPE_STORE)) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        $store = $this->storeManager->getStore();
        $limit = (int)$this->scopeConfig->getValue('etechflow_blog/rss/posts_limit', ScopeInterface::SCOPE_STORE) ?: 20;

        $collection = $this->collectionFactory->create();
        $collection->addActiveFilter()
            ->addStoreFilter((int)$store->getId())
            ->addFieldToFilter('publish_time', ['lteq' => $this->date->gmtDate()])
            ->setRecentOrder()
            ->setPageSize($limit);

        $title = htmlspecialchars($store->getName() . ' — ' . ($this->scopeConfig->getValue('etechflow_blog/general/title', ScopeInterface::SCOPE_STORE) ?: 'Blog'));
        $link = htmlspecialchars($this->url->getBaseUrl());

        $items = '';
        foreach ($collection as $post) {
            $excerpt = $post->getData('short_content')
                ?: mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags((string)$post->getData('content')))), 0, 300);
            $items .= '<item>'
                . '<title>' . htmlspecialchars((string)$post->getTitle()) . '</title>'
                . '<link>' . htmlspecialchars($this->url->getPostUrl($post)) . '</link>'
                . '<guid>' . htmlspecialchars($this->url->getPostUrl($post)) . '</guid>'
                . '<pubDate>' . date(DATE_RSS, strtotime((string)$post->getData('publish_time'))) . '</pubDate>'
                . '<description><![CDATA[' . $excerpt . ']]></description>'
                . '</item>';
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<rss version="2.0"><channel>'
            . '<title>' . $title . '</title>'
            . '<link>' . $link . '</link>'
            . '<description>' . $title . '</description>'
            . $items
            . '</channel></rss>';

        /** @var \Magento\Framework\Controller\Result\Raw $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
        $result->setContents($xml);
        return $result;
    }
}
