<?php
/**
 * Front controller router that turns clean blog URLs into module actions.
 * Matches only paths under the configured base route and forwards to the right
 * controller/action; an unrecognised first segment is treated as a post slug
 * (so /blog/my-post resolves to Post/View).
 */
declare(strict_types=1);

namespace Etechflow\Blog\Controller;

use Etechflow\Blog\Model\LicenseValidator;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Store\Model\ScopeInterface;

class Router implements RouterInterface
{
    /** Route id declared in etc/frontend/routes.xml */
    private const ROUTE_ID = 'blog';

    /** @var ActionFactory */
    private $actionFactory;
    /** @var ScopeConfigInterface */
    private $scopeConfig;
    /** @var LicenseValidator */
    private $licenseValidator;

    public function __construct(
        ActionFactory $actionFactory,
        ScopeConfigInterface $scopeConfig,
        LicenseValidator $licenseValidator
    ) {
        $this->actionFactory = $actionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->licenseValidator = $licenseValidator;
    }

    /**
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        if (!$this->scopeConfig->isSetFlag('etechflow_blog/general/enabled', ScopeInterface::SCOPE_STORE)) {
            return null;
        }

        // License gate: an unlicensed install serves no blog pages (silent 404).
        if (!$this->licenseValidator->isValid()) {
            return null;
        }

        // Prevent re-interception after forward('noroute') — avoids infinite loop
        if ($request->getActionName() === 'noroute') {
            return null;
        }

        $route = (string)$this->scopeConfig->getValue('etechflow_blog/general/route', ScopeInterface::SCOPE_STORE);
        $route = $route !== '' ? trim($route, '/') : 'blog';

        $path = trim($request->getPathInfo(), '/');
        $parts = $path === '' ? [] : explode('/', $path);

        if (empty($parts) || $parts[0] !== $route) {
            return null;
        }
        array_shift($parts);

        $controller = 'index';
        $action = 'index';
        $params = [];

        if (!empty($parts)) {
            $first = $parts[0];
            switch ($first) {
                case 'post':
                    $controller = 'post'; $action = 'view';
                    if (isset($parts[1])) { $params['identifier'] = $parts[1]; }
                    break;
                case 'category':
                    $controller = 'category'; $action = 'view';
                    if (isset($parts[1])) { $params['identifier'] = $parts[1]; }
                    break;
                case 'tag':
                    $controller = 'tag'; $action = 'view';
                    if (isset($parts[1])) { $params['identifier'] = $parts[1]; }
                    break;
                case 'author':
                    $controller = 'author'; $action = 'view';
                    if (isset($parts[1])) { $params['identifier'] = $parts[1]; }
                    break;
                case 'archive':
                    $controller = 'archive'; $action = 'view';
                    if (isset($parts[1])) { $params['year'] = $parts[1]; }
                    if (isset($parts[2])) { $params['month'] = $parts[2]; }
                    break;
                case 'search':
                    $controller = 'search'; $action = 'index';
                    if (isset($parts[1])) { $params['q'] = urldecode($parts[1]); }
                    break;
                case 'rss':
                    $controller = 'rss'; $action = 'feed';
                    break;
                default:
                    // Pretty post permalink: /blog/{identifier}
                    $controller = 'post'; $action = 'view';
                    $params['identifier'] = $first;
                    break;
            }
        }

        $request->setModuleName(self::ROUTE_ID)
            ->setControllerName($controller)
            ->setActionName($action);
        foreach ($params as $key => $value) {
            $request->setParam($key, $value);
        }
        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $path);

        return $this->actionFactory->create(Forward::class);
    }
}
