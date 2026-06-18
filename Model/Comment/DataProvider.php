<?php
declare(strict_types=1);

namespace Etechflow\Blog\Model\Comment;

use Etechflow\Blog\Model\ResourceModel\Comment\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /** @var array|null */
    private $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }
        $this->loadedData = [];
        foreach ($this->collection->getItems() as $comment) {
            $this->loadedData[$comment->getId()] = $comment->getData();
        }
        return $this->loadedData;
    }
}
