<?php
/**
 * "Recent Posts" CMS/layout widget.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Block\Widget;

use Etechflow\Blog\Model\ResourceModel\Post\CollectionFactory;
use Etechflow\Blog\Model\Url;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;

class RecentPosts extends Template implements BlockInterface
{
    protected $_template = 'Etechflow_Blog::widget/recent.phtml';

    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var Url */
    private $url;
    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Url $url,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->url = $url;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getUrlModel(): Url
    {
        return $this->url;
    }

    public function getPosts(): array
    {
        $count = (int)$this->getData('count') ?: 3;
        $collection = $this->collectionFactory->create();
        $collection->addActiveFilter()
            ->addStoreFilter((int)$this->storeManager->getStore()->getId())
            ->setRecentOrder()
            ->setPageSize($count);
        return $collection->getItems();
    }

    public function getHeading(): string
    {
        return (string)($this->getData('title') ?: __('Latest from the blog'));
    }

    public function getThumbnail($post): string
    {
        return (string)($post->getData('featured_list_img') ?: $post->getData('featured_img'));
    }
}
