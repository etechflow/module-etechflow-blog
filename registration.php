<?php
/**
 * Etechflow_Blog
 * A fast, SEO-friendly, theme-agnostic blog for Magento 2 / Adobe Commerce.
 * Works on Hyvä, Luma and Adobe Commerce themes.
 */
declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Etechflow_Blog',
    __DIR__
);
