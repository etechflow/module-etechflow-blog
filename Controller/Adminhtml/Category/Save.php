<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Category;

use Etechflow\Blog\Api\CategoryRepositoryInterface;
use Etechflow\Blog\Model\CategoryFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::category';

    /** @var CategoryFactory */
    private $categoryFactory;
    /** @var CategoryRepositoryInterface */
    private $categoryRepository;
    /** @var DataPersistorInterface */
    private $dataPersistor;

    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $redirect->setPath('*/*/');
        }
        foreach (['is_active', 'include_in_menu'] as $flag) {
            if (isset($data[$flag])) {
                $data[$flag] = (int)(bool)$data[$flag];
            }
        }
        $id = (int)($data['category_id'] ?? 0);
        try {
            $model = $id ? $this->categoryRepository->getById($id) : $this->categoryFactory->create();
            if (!$id) {
                unset($data['category_id']);
            }
            $model->addData($data);
            $this->categoryRepository->save($model);
            $this->messageManager->addSuccessMessage(__('The category has been saved.'));
            $this->dataPersistor->clear('etechflow_blog_category');
            if ($this->getRequest()->getParam('back')) {
                return $redirect->setPath('*/*/edit', ['category_id' => $model->getId()]);
            }
            return $redirect->setPath('*/*/');
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This category no longer exists.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('etechflow_blog_category', $data);
            return $redirect->setPath('*/*/edit', ['category_id' => $id]);
        }
        return $redirect->setPath('*/*/');
    }
}
