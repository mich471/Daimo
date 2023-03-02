<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractEavSave extends Action
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * AbstractEavSave constructor.
     *
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function initCurrentStore(): int
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());
        return $storeId;
    }

    /**
     * Check "Use Default Value" checkboxes values
     *
     * @param array                                            $postData
     * @param \Magento\Framework\Model\AbstractExtensibleModel $model
     * @return $this
     */
    protected function prepareUseDefault(array $postData, AbstractExtensibleModel $model)
    {
        if (isset($postData['use_default']) && ! empty($postData['use_default'])) {
            foreach ($postData['use_default'] as $attributeCode => $attributeValue) {
                if ($attributeValue) {
                    $model->setData($attributeCode, null);
                }
            }
        }

        return $this;
    }
}
