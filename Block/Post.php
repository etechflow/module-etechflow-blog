<?php
/**
 * Single-post block. Renders the article and all of its features: author box,
 * categories/tags, related posts & products, nested comments, next/prev,
 * reading time and an auto table of contents built from the body headings.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Block;

use Etechflow\Blog\Api\AuthorRepositoryInterface;
use Etechflow\Blog\Model\Comment;
use Etechflow\Blog\Model\PageContext;
use Etechflow\Blog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Etechflow\Blog\Model\ResourceModel\Comment\CollectionFactory as CommentCollectionFactory;
use Etechflow\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Etechflow\Blog\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;
use Etechflow\Blog\Model\Url;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Post extends Template
{
    /** @var PageContext */
    private $pageContext;
    /** @var Url */
    private $url;
    /** @var FilterProvider */
    private $filterProvider;
    /** @var CategoryCollectionFactory */
    private $categoryCollectionFactory;
    /** @var TagCollectionFactory */
    private $tagCollectionFactory;
    /** @var CommentCollectionFactory */
    private $commentCollectionFactory;
    /** @var PostCollectionFactory */
    private $postCollectionFactory;
    /** @var AuthorRepositoryInterface */
    private $authorRepository;
    /** @var ProductCollectionFactory */
    private $productCollectionFactory;
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var PriceCurrencyInterface */
    private $priceCurrency;
    /** @var array|null */
    private $processed;

    public function __construct(
        Context $context,
        PageContext $pageContext,
        Url $url,
        FilterProvider $filterProvider,
        CategoryCollectionFactory $categoryCollectionFactory,
        TagCollectionFactory $tagCollectionFactory,
        CommentCollectionFactory $commentCollectionFactory,
        PostCollectionFactory $postCollectionFactory,
        AuthorRepositoryInterface $authorRepository,
        ProductCollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->pageContext = $pageContext;
        $this->url = $url;
        $this->filterProvider = $filterProvider;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->commentCollectionFactory = $commentCollectionFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->authorRepository = $authorRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    public function getPost()
    {
        return $this->pageContext->getPost();
    }

    public function getUrlModel(): Url
    {
        return $this->url;
    }

    /**
     * Process the body once: run CMS directives/widgets and inject heading
     * anchors; cache the resulting html + table of contents.
     */
    private function process(): array
    {
        if ($this->processed !== null) {
            return $this->processed;
        }
        $post = $this->getPost();
        $html = $post ? (string)$post->getData('content') : '';
        $html = $this->filterProvider->getPageFilter()->filter($html);

        $toc = [];
        $html = preg_replace_callback(
            '/<(h[23])([^>]*)>(.*?)<\/\1>/is',
            function ($m) use (&$toc) {
                $level = (int)substr($m[1], 1);
                $text = trim(strip_tags($m[3]));
                $id = 'etf-h-' . (count($toc) + 1) . '-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($text));
                $id = trim($id, '-');
                $toc[] = ['level' => $level, 'id' => $id, 'text' => $text];
                $attrs = $m[2];
                if (strpos($attrs, 'id=') === false) {
                    $attrs .= ' id="' . $id . '"';
                }
                return '<' . $m[1] . $attrs . '>' . $m[3] . '</' . $m[1] . '>';
            },
            $html
        );

        $this->processed = ['html' => $html, 'toc' => $toc];
        return $this->processed;
    }

    public function getContent(): string
    {
        return $this->process()['html'];
    }

    public function getTableOfContents(): array
    {
        return $this->process()['toc'];
    }

    public function getReadingTime(): int
    {
        $post = $this->getPost();
        $words = $post ? str_word_count(strip_tags((string)$post->getData('content'))) : 0;
        return max(1, (int)ceil($words / 200));
    }

    public function getAuthor()
    {
        $post = $this->getPost();
        if (!$post || !$post->getData('author_id')) {
            return null;
        }
        try {
            return $this->authorRepository->getById((int)$post->getData('author_id'));
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /** @return array */
    public function getCategories(): array
    {
        $ids = (array)($this->getPost() ? $this->getPost()->getData('categories') : []);
        if (!$ids) {
            return [];
        }
        $collection = $this->categoryCollectionFactory->create();
        $collection->addActiveFilter()->addFieldToFilter('category_id', ['in' => $ids]);
        return $collection->getItems();
    }

    /** @return array */
    public function getTags(): array
    {
        $ids = (array)($this->getPost() ? $this->getPost()->getData('tags') : []);
        if (!$ids) {
            return [];
        }
        $collection = $this->tagCollectionFactory->create();
        $collection->addActiveFilter()->addFieldToFilter('tag_id', ['in' => $ids]);
        return $collection->getItems();
    }

    /** @return array */
    public function getRelatedPosts(): array
    {
        $post = $this->getPost();
        if (!$post) {
            return [];
        }
        $limit = (int)$this->_scopeConfig->getValue('etechflow_blog/related_posts/number', ScopeInterface::SCOPE_STORE) ?: 4;
        $ids = (array)$post->getData('related_posts');
        $collection = $this->postCollectionFactory->create();
        $collection->addActiveFilter();
        if ($ids) {
            $collection->addFieldToFilter('post_id', ['in' => $ids]);
        } else {
            // Fallback: newest other posts
            $collection->addFieldToFilter('post_id', ['neq' => $post->getId()])->setRecentOrder();
        }
        $collection->setPageSize($limit);
        return $collection->getItems();
    }

    /** @return array list of ['name','url','image','price'] */
    public function getRelatedProducts(): array
    {
        $post = $this->getPost();
        $ids = $post ? (array)$post->getData('related_products') : [];
        if (!$ids) {
            return [];
        }
        $result = [];
        try {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect(['name', 'image', 'small_image'])
                ->addFieldToFilter('entity_id', ['in' => $ids])
                ->addAttributeToFilter('status', 1);
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            foreach ($collection as $product) {
                $img = $product->getData('small_image') ?: $product->getData('image');
                $result[] = [
                    'name' => $product->getName(),
                    'url' => $product->getProductUrl(),
                    'image' => $img ? $mediaUrl . 'catalog/product' . $img : '',
                    'price' => $this->priceCurrency->format($product->getFinalPrice(), false),
                ];
            }
        } catch (\Exception $e) {
            return [];
        }
        return $result;
    }

    /** @return array nested comments [comment, children[]] */
    public function getComments(): array
    {
        $post = $this->getPost();
        if (!$post) {
            return [];
        }
        $collection = $this->commentCollectionFactory->create();
        $collection->addPostFilter((int)$post->getId())->addApprovedFilter()->setChronologicalOrder();

        $byParent = [];
        foreach ($collection as $comment) {
            $byParent[(int)$comment->getData('parent_id')][] = $comment;
        }
        return $this->buildTree($byParent, 0);
    }

    private function buildTree(array $byParent, int $parentId): array
    {
        $out = [];
        foreach ($byParent[$parentId] ?? [] as $comment) {
            $out[] = ['comment' => $comment, 'children' => $this->buildTree($byParent, (int)$comment->getId())];
        }
        return $out;
    }

    public function getNextPost()
    {
        return $this->adjacentPost('gt', 'ASC');
    }

    public function getPrevPost()
    {
        return $this->adjacentPost('lt', 'DESC');
    }

    private function adjacentPost(string $op, string $dir)
    {
        $post = $this->getPost();
        if (!$post || !$post->getData('publish_time')) {
            return null;
        }
        $collection = $this->postCollectionFactory->create();
        $collection->addActiveFilter()
            ->addFieldToFilter('publish_time', [$op => $post->getData('publish_time')])
            ->setOrder('publish_time', $dir)
            ->setPageSize(1);
        $item = $collection->getFirstItem();
        return $item->getId() ? $item : null;
    }

    public function getShareUrl(string $network, string $postUrl, string $title): string
    {
        $u = rawurlencode($postUrl);
        $t = rawurlencode($title);
        $map = [
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $u,
            'twitter' => 'https://twitter.com/intent/tweet?url=' . $u . '&text=' . $t,
            'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $u,
            'whatsapp' => 'https://api.whatsapp.com/send?text=' . $t . '%20' . $u,
            'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . $u . '&description=' . $t,
            'email' => 'mailto:?subject=' . $t . '&body=' . $u,
        ];
        return $map[$network] ?? '#';
    }

    public function getCommentPostUrl(): string
    {
        return $this->getUrl('blog/comment/post');
    }
}
