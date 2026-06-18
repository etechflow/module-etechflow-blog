<?php
/**
 * Feeds existing post data into the admin form (and restores unsaved input
 * after a validation error via the data persistor).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\Post;

use Etechflow\Blog\Model\ResourceModel\Post\CollectionFactory;
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
        foreach ($this->collection->getItems() as $post) {
            $this->loadedData[$post->getId()] = $post->getData();
        }

        $persisted = $this->dataPersistor->get('etechflow_blog_post');
        if (!empty($persisted)) {
            $postId = $persisted['post_id'] ?? null;
            $this->loadedData[$postId] = $persisted;
            $this->dataPersistor->clear('etechflow_blog_post');
        }
        return $this->loadedData;
    }
}
