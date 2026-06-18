<?php
declare(strict_types=1);

namespace Etechflow\Blog\Block\Adminhtml\Post\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        if (!$this->getPostId()) {
            return [];
        }
        return [
            'label' => __('Delete Post'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\'' . __('Are you sure you want to delete this post?')
                . '\', \'' . $this->getUrl('*/*/delete', ['post_id' => $this->getPostId()]) . '\')',
            'sort_order' => 20,
        ];
    }
}
