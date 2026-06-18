<?php
/**
 * Enabled/Disabled option source — shared by admin grids and forms.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 1, 'label' => __('Enabled')],
            ['value' => 0, 'label' => __('Disabled')],
        ];
    }
}
