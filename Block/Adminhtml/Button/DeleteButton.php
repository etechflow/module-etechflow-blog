<?php
declare(strict_types=1);

namespace Etechflow\Blog\Block\Adminhtml\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        $id = $this->getEntityId();
        if (!$id) {
            return [];
        }
        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\'' . __('Are you sure you want to delete this item?')
                . '\', \'' . $this->getUrl('*/*/delete', [$this->getIdFieldName() => $id]) . '\')',
            'sort_order' => 20,
        ];
    }
}
