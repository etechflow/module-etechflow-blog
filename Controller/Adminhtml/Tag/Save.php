<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Tag;

use Etechflow\Blog\Api\TagRepositoryInterface;
use Etechflow\Blog\Model\TagFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::tag';

    /** @var TagFactory */
    private $tagFactory;
    /** @var TagRepositoryInterface */
    private $tagRepository;
    /** @var DataPersistorInterface */
    private $dataPersistor;

    public function __construct(
        Context $context,
        TagFactory $tagFactory,
        TagRepositoryInterface $tagRepository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->tagFactory = $tagFactory;
        $this->tagRepository = $tagRepository;
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
        $id = (int)($data['tag_id'] ?? 0);
        try {
            $model = $id ? $this->tagRepository->getById($id) : $this->tagFactory->create();
            if (!$id) {
                unset($data['tag_id']);
            }
            $model->addData($data);
            $this->tagRepository->save($model);
            $this->messageManager->addSuccessMessage(__('The tag has been saved.'));
            $this->dataPersistor->clear('etechflow_blog_tag');
            if ($this->getRequest()->getParam('back')) {
                return $redirect->setPath('*/*/edit', ['tag_id' => $model->getId()]);
            }
            return $redirect->setPath('*/*/');
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This tag no longer exists.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('etechflow_blog_tag', $data);
            return $redirect->setPath('*/*/edit', ['tag_id' => $id]);
        }
        return $redirect->setPath('*/*/');
    }
}
