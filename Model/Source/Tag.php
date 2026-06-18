<?php
/**
 * Tag option source — populates the "Tags" multiselect on the post form.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\Source;

use Etechflow\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Tag implements OptionSourceInterface
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
        $collection->addFieldToSelect(['tag_id', 'title'])->setOrder('title', 'ASC');
        foreach ($collection as $tag) {
            $this->options[] = ['value' => (int)$tag->getId(), 'label' => $tag->getData('title')];
        }
        return $this->options;
    }
}
