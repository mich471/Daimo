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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Plugin\Magento\Cms\Block\Adminhtml\Page\Edit;

class SaveButtonPlugin
{
    const ACTION_FOR_EXTEND = 'save';
    const EXTEND_METHOD_PREFIX = 'prGdpr';

    /**
     * @var \Plumrocket\GDPR\Model\Magento\VersionProvider
     */
    private $versionProvider;

    /**
     * SaveButtonPlugin constructor.
     *
     * @param \Plumrocket\GDPR\Model\Magento\VersionProvider $versionProvider
     */
    public function __construct(\Plumrocket\GDPR\Model\Magento\VersionProvider $versionProvider)
    {
        $this->versionProvider = $versionProvider;
    }

    /**
     * @param \Magento\Cms\Block\Adminhtml\Page\Edit\SaveButton $subject
     * @param                                                   $result
     * @return mixed
     */
    public function afterGetButtonData(//@codingStandardsIgnoreLine
        \Magento\Cms\Block\Adminhtml\Page\Edit\SaveButton $subject,
        $result
    ) {
        if (! $this->versionProvider->isMagentoVersionBelow('2.3.0')) {
            $result = $this->changeButtonActionName($result);

            if (isset($result['options'])) {
                $result['options'] = array_map([$this, 'changeButtonActionName'], $result['options']);
            }
        }

        return $result;
    }

    /**
     * @param string $actionName
     * @return string
     */
    private function getOverwriteName($actionName)
    {
        return self::EXTEND_METHOD_PREFIX . ucfirst($actionName);
    }

    /**
     * @param array  $result
     * @param string $actionName
     * @return array
     */
    private function changeButtonActionName(array $result, $actionName = self::ACTION_FOR_EXTEND)
    {
        $actions = $result['data_attribute']['mage-init']['buttonAdapter']['actions'];

        $newActions = [];
        foreach ($actions as $key => $params) {
            if (isset($params['actionName']) && $actionName === $params['actionName']) {
                $params['actionName'] = $this->getOverwriteName($params['actionName']);
                $newActions[$key] = $params;
            }
        }

        $result['data_attribute']['mage-init']['buttonAdapter']['actions'] = $newActions;

        return $result;
    }
}
