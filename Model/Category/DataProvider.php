<?php
declare(strict_types=1);

namespace Etechflow\Blog\Model\Category;

use Etechflow\Blog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /** @var DataPersistorInterface */
    private $dataPersistor;
    /** @var array|null */
    private $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }
        $this->loadedData = [];
        foreach ($this->collection->getItems() as $category) {
            $this->loadedData[$category->getId()] = $category->getData();
        }
        $persisted = $this->dataPersistor->get('etechflow_blog_category');
        if (!empty($persisted)) {
            $id = $persisted['category_id'] ?? null;
            $this->loadedData[$id] = $persisted;
            $this->dataPersistor->clear('etechflow_blog_category');
        }
        return $this->loadedData;
    }
}
