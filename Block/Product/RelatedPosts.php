<?php
/**
 * Shows blog posts linked to the current catalog product (config-toggled).
 * Great for buying guides / how-tos on the product page.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Block\Product;

use Etechflow\Blog\Model\LicenseValidator;
use Etechflow\Blog\Model\ResourceModel\Post\CollectionFactory;
use Etechflow\Blog\Model\Url;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class RelatedPosts extends Template
{
    /** @var Registry */
    private $registry;
    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var ResourceConnection */
    private $resource;
    /** @var Url */
    private $url;
    /** @var LicenseValidator */
    private $licenseValidator;

    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $collectionFactory,
        ResourceConnection $resource,
        Url $url,
        LicenseValidator $licenseValidator,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
        $this->url = $url;
        $this->licenseValidator = $licenseValidator;
        parent::__construct($context, $data);
    }

    public function getUrlModel(): Url
    {
        return $this->url;
    }

    /** @return array */
    public function getPosts(): array
    {
        if (!$this->licenseValidator->isValid()) {
            return [];
        }
        if (!$this->_scopeConfig->isSetFlag('etechflow_blog/product_page/related_posts', ScopeInterface::SCOPE_STORE)) {
            return [];
        }
        $product = $this->registry->registry('current_product');
        if (!$product) {
            return [];
        }
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('etechflow_blog_post_relatedproduct');
        $postIds = $connection->fetchCol(
            $connection->select()->from($table, 'post_id')->where('related_id = ?', (int)$product->getId())
        );
        if (!$postIds) {
            return [];
        }
        $count = (int)$this->_scopeConfig->getValue('etechflow_blog/product_page/related_posts_count', ScopeInterface::SCOPE_STORE) ?: 3;
        $collection = $this->collectionFactory->create();
        $collection->addActiveFilter()
            ->addFieldToFilter('post_id', ['in' => $postIds])
            ->setRecentOrder()
            ->setPageSize($count);
        return $collection->getItems();
    }

    public function getThumbnail($post): string
    {
        return (string)($post->getData('featured_list_img') ?: $post->getData('featured_img'));
    }
}
