<?php
/**
 * Plugin for the template engine factory that makes a decision of whether to activate debugging hints or not
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Core\Model\TemplateEngine\Plugin;

use Magento\Store\Model\ScopeInterface;

class DebugHints
{
    /**#@+
     * XPath of configuration of the debugging hints
     */
    const XML_PATH_DEBUG_TEMPLATE_HINTS = 'dev/debug/template_hints';

    const XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS = 'dev/debug/template_hints_blocks';

    /**#@-*/

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Config\Helper\Data
     */
    private $configHelper;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\Helper\Data $configHelper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Helper\Data $configHelper
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->configHelper = $configHelper;
    }

    /**
     * Wrap template engine instance with the debugging hints decorator, depending of the store configuration
     *
     * @param \Magento\Framework\View\TemplateEngineFactory $subject
     * @param \Magento\Framework\View\TemplateEngineInterface $invocationResult
     *
     * @return \Magento\Framework\View\TemplateEngineInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreate(
        \Magento\Framework\View\TemplateEngineFactory $subject,
        \Magento\Framework\View\TemplateEngineInterface $invocationResult
    ) {
        if ($this->scopeConfig->getValue(self::XML_PATH_DEBUG_TEMPLATE_HINTS, ScopeInterface::SCOPE_STORE) &&
            $this->configHelper->isDevAllowed()) {
            $showBlockHints = $this->scopeConfig->getValue(
                self::XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS,
                ScopeInterface::SCOPE_STORE
            );
            return $this->objectManager->create(
                'Magento\Core\Model\TemplateEngine\Decorator\DebugHints',
                ['subject' => $invocationResult, 'showBlockHints' => $showBlockHints]
            );
        }
        return $invocationResult;
    }
}
