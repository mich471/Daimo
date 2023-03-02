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

namespace Plumrocket\GDPR\Model\Config\Source;

class ConsentLocationTypes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Plumrocket\GDPR\Api\ConsentLocationTypeInterface
     */
    private $consentLocationType;

    /**
     * ConsentLocations constructor.
     *
     * @param \Plumrocket\GDPR\Api\ConsentLocationTypeInterface $consentLocationType
     */
    public function __construct(
        \Plumrocket\GDPR\Api\ConsentLocationTypeInterface $consentLocationType
    ) {
        $this->consentLocationType = $consentLocationType;
    }

    /**
     * @return array
     */
    public function toOptionArray() : array
    {
        $options = [];

        foreach ($this->consentLocationType->getList() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $options;
    }
}
