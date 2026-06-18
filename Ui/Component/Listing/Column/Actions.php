<?php
/**
 * Configurable Edit/Delete actions column reused by every grid. The id field and
 * edit/delete routes are passed in from the listing XML, so no per-entity class
 * is needed.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    /** @var UrlInterface */
    private $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }
        $name = $this->getData('name');
        $idField = $this->getData('config/indexField') ?: 'id';
        $editPath = $this->getData('config/editUrlPath');
        $deletePath = $this->getData('config/deleteUrlPath');

        foreach ($dataSource['data']['items'] as &$item) {
            if (empty($item[$idField])) {
                continue;
            }
            $id = $item[$idField];
            if ($editPath) {
                $item[$name]['edit'] = [
                    'href' => $this->urlBuilder->getUrl($editPath, [$idField => $id]),
                    'label' => __('Edit'),
                ];
            }
            if ($deletePath) {
                $item[$name]['delete'] = [
                    'href' => $this->urlBuilder->getUrl($deletePath, [$idField => $id]),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete'),
                        'message' => __('Are you sure you want to delete this item?'),
                    ],
                    'post' => true,
                ];
            }
        }
        return $dataSource;
    }
}
