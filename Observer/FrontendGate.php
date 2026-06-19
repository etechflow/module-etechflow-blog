<?php
/**
 * Storefront license gate. The custom Router only protects the pretty-permalink
 * paths (/blog/{slug}, /blog/category/{slug} ...); the blog home (/blog ->
 * Index/Index) and any controller reached directly via the standard router
 * (frontName "blog") bypass it. This predispatch observer fires for EVERY blog
 * storefront action and returns a 404 when the module is unlicensed, so an
 * unlicensed/suspended install serves no blog content anywhere.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Observer;

use Etechflow\Blog\Model\LicenseValidator;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class FrontendGate implements ObserverInterface
{
    /** @var LicenseValidator */
    private $licenseValidator;
    /** @var ActionFlag */
    private $actionFlag;
    /** @var ResponseInterface */
    private $response;

    public function __construct(
        LicenseValidator $licenseValidator,
        ActionFlag $actionFlag,
        ResponseInterface $response
    ) {
        $this->licenseValidator = $licenseValidator;
        $this->actionFlag = $actionFlag;
        $this->response = $response;
    }

    public function execute(Observer $observer)
    {
        if ($this->licenseValidator->isValid()) {
            return;
        }
        // Unlicensed: stop the action and return 404 (page blocked, not cacheable).
        $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        $this->response->setHttpResponseCode(404);
    }
}
