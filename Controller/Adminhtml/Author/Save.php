<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Author;

use Etechflow\Blog\Api\AuthorRepositoryInterface;
use Etechflow\Blog\Model\AuthorFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::author';

    /** @var AuthorFactory */
    private $authorFactory;
    /** @var AuthorRepositoryInterface */
    private $authorRepository;
    /** @var DataPersistorInterface */
    private $dataPersistor;

    public function __construct(
        Context $context,
        AuthorFactory $authorFactory,
        AuthorRepositoryInterface $authorRepository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->authorFactory = $authorFactory;
        $this->authorRepository = $authorRepository;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $redirect->setPath('*/*/');
        }
        if (isset($data['is_active'])) {
            $data['is_active'] = (int)(bool)$data['is_active'];
        }
        if (isset($data['image']) && is_array($data['image'])) {
            $first = reset($data['image']);
            $data['image'] = is_array($first) ? (string)($first['name'] ?? '') : (string)$first;
        }
        $id = (int)($data['author_id'] ?? 0);
        try {
            $model = $id ? $this->authorRepository->getById($id) : $this->authorFactory->create();
            if (!$id) {
                unset($data['author_id']);
            }
            $model->addData($data);
            $this->authorRepository->save($model);
            $this->messageManager->addSuccessMessage(__('The author has been saved.'));
            $this->dataPersistor->clear('etechflow_blog_author');
            if ($this->getRequest()->getParam('back')) {
                return $redirect->setPath('*/*/edit', ['author_id' => $model->getId()]);
            }
            return $redirect->setPath('*/*/');
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This author no longer exists.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('etechflow_blog_author', $data);
            return $redirect->setPath('*/*/edit', ['author_id' => $id]);
        }
        return $redirect->setPath('*/*/');
    }
}
