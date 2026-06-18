<?php
/**
 * Save a post (create or update). Normalises checkbox/multiselect values,
 * persists relations (stores/categories/tags) via the resource model, and
 * repopulates the form on error using the data persistor.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Post;

use Etechflow\Blog\Api\PostRepositoryInterface;
use Etechflow\Blog\Model\PostFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::post';

    /** @var PostFactory */
    private $postFactory;
    /** @var PostRepositoryInterface */
    private $postRepository;
    /** @var DataPersistorInterface */
    private $dataPersistor;

    public function __construct(
        Context $context,
        PostFactory $postFactory,
        PostRepositoryInterface $postRepository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->postFactory = $postFactory;
        $this->postRepository = $postRepository;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $data = $this->prepareData($data);
        $postId = (int)($data['post_id'] ?? 0);

        try {
            if ($postId) {
                $model = $this->postRepository->getById($postId);
            } else {
                $model = $this->postFactory->create();
                unset($data['post_id']);
            }
            $model->addData($data);
            $this->postRepository->save($model);

            $this->messageManager->addSuccessMessage(__('The post has been saved.'));
            $this->dataPersistor->clear('etechflow_blog_post');

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['post_id' => $model->getId()]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This post no longer exists.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('etechflow_blog_post', $data);
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $postId]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Normalise incoming form values to DB-friendly shapes.
     */
    private function prepareData(array $data): array
    {
        foreach (['is_active', 'include_in_recent', 'is_comments_enabled'] as $flag) {
            if (isset($data[$flag])) {
                $data[$flag] = (int)(bool)$data[$flag];
            }
        }
        // FileUploader components post an array of {name,url}; keep the stored name.
        foreach (['featured_img', 'featured_list_img', 'banner_img_desktop', 'banner_img_tablet', 'banner_img_mobile', 'og_img'] as $img) {
            if (isset($data[$img]) && is_array($data[$img])) {
                $first = reset($data[$img]);
                $data[$img] = is_array($first) ? (string)($first['name'] ?? '') : (string)$first;
            }
        }
        if (empty($data['publish_time'])) {
            unset($data['publish_time']);
        }
        return $data;
    }
}
