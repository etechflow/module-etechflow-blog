<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Tag;

use Etechflow\Blog\Api\TagRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::tag';

    /** @var PageFactory */
    private $resultPageFactory;
    /** @var TagRepositoryInterface */
    private $tagRepository;

    public function __construct(Context $context, PageFactory $resultPageFactory, TagRepositoryInterface $tagRepository)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->tagRepository = $tagRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('tag_id');
        if ($id) {
            try {
                $this->tagRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This tag no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }
        $page = $this->resultPageFactory->create();
        $page->setActiveMenu('Etechflow_Blog::tag');
        $page->getConfig()->getTitle()->prepend($id ? __('Edit Tag') : __('New Tag'));
        return $page;
    }
}
