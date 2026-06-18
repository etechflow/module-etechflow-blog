<?php
/**
 * Shared helper for post form buttons (resolves the current id + URLs).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Block\Adminhtml\Post\Edit;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    /** @var Context */
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getPostId(): int
    {
        return (int)$this->context->getRequest()->getParam('post_id');
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
