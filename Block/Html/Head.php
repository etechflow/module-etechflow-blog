<?php
/**
 * Emits SEO head tags for blog pages: canonical, Open Graph, Twitter cards and
 * JSON-LD structured data (BlogPosting + BreadcrumbList). All toggled by config.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Block\Html;

use Etechflow\Blog\Api\AuthorRepositoryInterface;
use Etechflow\Blog\Model\PageContext;
use Etechflow\Blog\Model\Url;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Head extends Template
{
    /** @var PageContext */
    private $pageContext;
    /** @var Url */
    private $url;
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var AuthorRepositoryInterface */
    private $authorRepository;

    public function __construct(
        Context $context,
        PageContext $pageContext,
        Url $url,
        StoreManagerInterface $storeManager,
        AuthorRepositoryInterface $authorRepository,
        array $data = []
    ) {
        $this->pageContext = $pageContext;
        $this->url = $url;
        $this->storeManager = $storeManager;
        $this->authorRepository = $authorRepository;
        parent::__construct($context, $data);
    }

    public function flag(string $path): bool
    {
        return $this->_scopeConfig->isSetFlag('etechflow_blog/' . $path, ScopeInterface::SCOPE_STORE);
    }

    public function getCanonicalUrl(): string
    {
        $ctx = $this->pageContext;
        switch ($ctx->getPageType()) {
            case PageContext::TYPE_POST:
                return $ctx->getPost() ? $this->url->getPostUrl($ctx->getPost()) : '';
            case PageContext::TYPE_CATEGORY:
                return $ctx->getCategory() ? $this->url->getCategoryUrl($ctx->getCategory()) : '';
            case PageContext::TYPE_TAG:
                return $ctx->getTag() ? $this->url->getTagUrl($ctx->getTag()) : '';
            case PageContext::TYPE_AUTHOR:
                return $ctx->getAuthor() ? $this->url->getAuthorUrl($ctx->getAuthor()) : '';
            default:
                return $this->url->getBaseUrl();
        }
    }

    public function mediaUrl(?string $path): string
    {
        $path = (string)$path;
        if ($path === '') {
            return '';
        }
        if (preg_match('#^(https?:)?//#', $path) || strpos($path, '/') === 0) {
            return $path;
        }
        return rtrim($this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA), '/') . '/' . ltrim($path, '/');
    }

    /** @return array<string,string> */
    public function getOpenGraph(): array
    {
        $ctx = $this->pageContext;
        $tags = [
            'og:site_name' => $this->storeManager->getStore()->getName(),
            'og:url' => $this->getCanonicalUrl(),
            'og:type' => 'website',
        ];
        if ($ctx->getPageType() === PageContext::TYPE_POST && $ctx->getPost()) {
            $post = $ctx->getPost();
            $tags['og:type'] = 'article';
            $tags['og:title'] = (string)($post->getData('og_title') ?: $post->getData('meta_title') ?: $post->getTitle());
            $tags['og:description'] = (string)($post->getData('og_description') ?: $post->getData('meta_description') ?: $post->getData('short_content'));
            $img = $this->mediaUrl((string)($post->getData('og_img') ?: $post->getData('featured_img')));
            if ($img) {
                $tags['og:image'] = $img;
            }
        }
        return array_filter($tags);
    }

    public function getJsonLd(): string
    {
        $ctx = $this->pageContext;
        if ($ctx->getPageType() !== PageContext::TYPE_POST || !$ctx->getPost()) {
            return '';
        }
        $post = $ctx->getPost();
        $authorName = '';
        if ($post->getData('author_id')) {
            try {
                $authorName = $this->authorRepository->getById((int)$post->getData('author_id'))->getTitle();
            } catch (NoSuchEntityException $e) {
                $authorName = '';
            }
        }
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->getTitle(),
            'mainEntityOfPage' => $this->url->getPostUrl($post),
            'datePublished' => (string)$post->getData('publish_time'),
            'dateModified' => (string)$post->getData('update_time'),
            'description' => (string)($post->getData('meta_description') ?: $post->getData('short_content')),
        ];
        $img = $this->mediaUrl((string)$post->getData('featured_img'));
        if ($img) {
            $data['image'] = $img;
        }
        if ($authorName) {
            $data['author'] = ['@type' => 'Person', 'name' => $authorName];
        }
        $data['publisher'] = [
            '@type' => 'Organization',
            'name' => $this->storeManager->getStore()->getName(),
        ];
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
