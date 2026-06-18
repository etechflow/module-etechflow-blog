<?php
/**
 * Category option source — populates the multiselect on the post form
 * (indented by tree level so the hierarchy is readable).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\Source;

use Etechflow\Blog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Category implements OptionSourceInterface
{
    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var array|null */
    private $options;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }
        $this->options = [];
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect(['category_id', 'title', 'level'])
            ->setOrder('path', 'ASC');
        foreach ($collection as $category) {
            $indent = str_repeat('— ', max(0, (int)$category->getData('level')));
            $this->options[] = [
                'value' => (int)$category->getId(),
                'label' => $indent . $category->getData('title'),
            ];
        }
        return $this->options;
    }
}
