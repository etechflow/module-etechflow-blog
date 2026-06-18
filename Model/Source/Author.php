<?php
/**
 * Author option source — populates the "Author" dropdown on the post form.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\Source;

use Etechflow\Blog\Model\ResourceModel\Author\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Author implements OptionSourceInterface
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
        $this->options = [['value' => '', 'label' => __('-- Please Select --')]];
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect(['author_id', 'title'])->setOrder('title', 'ASC');
        foreach ($collection as $author) {
            $this->options[] = ['value' => (int)$author->getId(), 'label' => $author->getData('title')];
        }
        return $this->options;
    }
}
