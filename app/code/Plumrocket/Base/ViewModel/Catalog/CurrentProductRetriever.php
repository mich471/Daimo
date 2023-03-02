<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\ViewModel\Catalog;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * @since 2.3.6
 */
class CurrentProductRetriever implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * GetCurrentProduct constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(Registry $coreRegistry)
    {
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @return null|\Magento\Catalog\Model\Product
     */
    public function get()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->coreRegistry->registry('product');

        return $product ?: null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->get() ? (int) $this->get()->getId() : 0;
    }
}
