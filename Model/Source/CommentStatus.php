<?php
/**
 * Comment moderation status options (grid filter, column, form).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\Source;

use Etechflow\Blog\Model\Comment;
use Magento\Framework\Data\OptionSourceInterface;

class CommentStatus implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => Comment::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => Comment::STATUS_APPROVED, 'label' => __('Approved')],
            ['value' => Comment::STATUS_NOT_APPROVED, 'label' => __('Not Approved')],
        ];
    }
}
