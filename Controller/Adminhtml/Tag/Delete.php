<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Tag;

use Etechflow\Blog\Api\TagRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::tag';

    /** @var TagRepositoryInterface */
    private $tagRepository;

    public function __construct(Context $context, TagRepositoryInterface $tagRepository)
    {
        parent::__construct($context);
        $this->tagRepository = $tagRepository;
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('tag_id');
        if ($id) {
            try {
                $this->tagRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The tag has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $redirect->setPath('*/*/edit', ['tag_id' => $id]);
            }
        }
        return $redirect->setPath('*/*/');
    }
}
