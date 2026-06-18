<?php
declare(strict_types=1);

namespace Etechflow\Blog\Controller\Adminhtml\Author;

use Etechflow\Blog\Api\AuthorRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Etechflow_Blog::author';

    /** @var AuthorRepositoryInterface */
    private $authorRepository;

    public function __construct(Context $context, AuthorRepositoryInterface $authorRepository)
    {
        parent::__construct($context);
        $this->authorRepository = $authorRepository;
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('author_id');
        if ($id) {
            try {
                $this->authorRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The author has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $redirect->setPath('*/*/edit', ['author_id' => $id]);
            }
        }
        return $redirect->setPath('*/*/');
    }
}
