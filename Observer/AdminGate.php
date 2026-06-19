<?php
/**
 * Admin license gate. When the module is unlicensed (suspended/expired/invalid),
 * every blog admin content page (Posts, Categories, Tags, Authors, Comments)
 * redirects to the in-admin License gate. The License controllers themselves
 * (gate/checkout/activated) are exempt so the merchant can still re-license, and
 * the Stores > Configuration page lives on a different route so it stays
 * reachable to paste a key.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Observer;

use Etechflow\Blog\Model\LicenseValidator;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

class AdminGate implements ObserverInterface
{
    /** @var LicenseValidator */
    private $licenseValidator;
    /** @var ActionFlag */
    private $actionFlag;
    /** @var ResponseInterface */
    private $response;
    /** @var UrlInterface */
    private $url;

    public function __construct(
        LicenseValidator $licenseValidator,
        ActionFlag $actionFlag,
        ResponseInterface $response,
        UrlInterface $url
    ) {
        $this->licenseValidator = $licenseValidator;
        $this->actionFlag = $actionFlag;
        $this->response = $response;
        $this->url = $url;
    }

    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        // Never gate the License controllers themselves (gate/checkout/activated),
        // otherwise the merchant could never reach the page to re-license.
        if (strtolower((string)$request->getControllerName()) === 'license') {
            return;
        }
        if ($this->licenseValidator->isValid()) {
            return;
        }
        // Block dispatch and send the admin to the license gate.
        $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        $this->response->setRedirect($this->url->getUrl('etechflow_blog/license/gate'));
    }
}
