<?php
/**
 * Generic form-button helper reused by the Category/Tag/Author/Comment forms.
 * Detects the entity id param ({entity}_id) automatically so one set of button
 * classes serves every entity.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Block\Adminhtml\Button;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    /** @var Context */
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getEntityId(): int
    {
        foreach ($this->context->getRequest()->getParams() as $key => $value) {
            if (preg_match('/_id$/', (string)$key) && is_numeric($value)) {
                return (int)$value;
            }
        }
        return 0;
    }

    public function getIdFieldName(): string
    {
        foreach (array_keys($this->context->getRequest()->getParams()) as $key) {
            if (preg_match('/_id$/', (string)$key)) {
                return (string)$key;
            }
        }
        return 'id';
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
